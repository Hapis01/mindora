<?php

namespace App\Services\AI;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\AssessmentScore;
use App\Models\User;

class AIService
{
    protected AIProviderInterface $provider;

    public function __construct(AIProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function getProvider(): AIProviderInterface
    {
        return $this->provider;
    }

    /**
     * Retrieve active conversation or initialize a new one with sanitized psychometric context.
     */
    public function getOrCreateConversation(User $user, ?AssessmentScore $score = null): AiConversation
    {
        $query = AiConversation::where('user_id', $user->id);

        if ($score) {
            $query->where('assessment_score_id', $score->id);
        } else {
            $query->whereNull('assessment_score_id');
        }

        $conversation = $query->latest()->first();

        if (!$conversation) {
            $title = $score
                ? "Konsultasi: " . ($score->archetype_label ?? ucfirst($score->assessment_type))
                : "Konsultasi Wawasan Psikometri Mindora";

            $conversation = AiConversation::create([
                'user_id' => $user->id,
                'assessment_score_id' => $score?->id,
                'title' => substr($title, 0, 150),
            ]);

            // Seed System Prompt with Sanitized Context (Strict Zero PII)
            $systemPrompt = $this->buildSystemPrompt($score);

            AiMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'system',
                'content' => $systemPrompt,
                'provider' => $this->provider->getProviderName(),
                'model' => 'system-rules',
                'tokens_used' => 0,
            ]);
        }

        return $conversation;
    }

    /**
     * Send user question to AI provider and record exchange in history.
     */
    public function askAssistant(AiConversation $conversation, string $userPrompt): AiMessage
    {
        // 1. Record User Message
        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => trim($userPrompt),
            'provider' => $this->provider->getProviderName(),
        ]);

        // 2. Build Message Thread for LLM (System + Recent Context)
        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->limit(12) // sliding context window
            ->get(['role', 'content'])
            ->toArray();

        // 3. Request LLM Inference
        $result = $this->provider->sendMessage($messages, [
            'max_tokens' => 4096,
        ]);

        // 4. Record Assistant Response
        return AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $result['content'],
            'provider' => $result['provider'] ?? $this->provider->getProviderName(),
            'model' => $result['model'] ?? ($this->provider->getProviderName() === 'gemini' ? 'gemini-3.6-flash' : 'deepseek-chat'),
            'tokens_used' => $result['tokens_used'] ?? 0,
        ]);
    }

    /**
     * Build system prompt with strict non-clinical rules and sanitized psychometric context.
     */
    public function buildSystemPrompt(?AssessmentScore $score = null): string
    {
        $context = $score ? $this->buildSanitizedContext($score) : "Tidak ada asesmen spesifik terpilih. Jawab seputar asesmen psikometri umum.";

        return <<<EOT
Anda adalah Mindora Insight Assistant, asisten kecerdasan buatan psikometri profesional untuk platform Mindora.

TUJUAN UTAMA ANDA:
1. Membantu pengguna memahami makna hasil asesmen psikometri dan kecenderungan perilaku mereka dengan bahasa Indonesia yang ramah, empatik, objektif, dan konstruktif.
2. Jelaskan data psikometri secara mudah dipahami tanpa jargon yang membingungkan.
3. Hubungkan profil kognitif dan kepribadian pengguna dengan strategi belajar, gaya kolaborasi tim, dan potensi eksplorasi karir.

PANDUAN FORMAT TULISAN (MUTLAK HARUS DIIKUTI):
- Tuliskan jawaban dalam teks biasa (plain text) yang bersih, rapi, dan mengalir secara alami.
- JANGAN PERNAH menggunakan format markdown tebal dengan tanda bintang (jangan gunakan **kata** ataupun *kata*). Tuliskan kata atau judul secara langsung sebagai teks biasa tanpa simbol tanda bintang (**) sama sekali.
- Jangan gunakan simbol pagar (#) untuk heading/judul. Gunakan jeda baris paragraf baru yang rapi atau daftar poin strip (-) / nomor (1, 2, 3) sederhana.
- Tuliskan penjelasan secara tuntas, utuh, dan tuntas hingga akhir paragraf. Jangan sampai jawaban terpotong.

ATURAN KEAMANAN & BATASAN KETAT (CRITICAL):
- BUKAN DIAGNOSIS KLINIS: Anda BUKAN dokter, psikolog klinis, ataupun psikiater. DILARANG KERAS memberikan diagnosis medis, evaluasi gangguan kepribadian (seperti depresi, bipolar, ADHD, skizofrenia, OCD), atau klaim kesehatan mental klinis.
- BUKAN TES IQ DETERMINISTIK: Dilarang mengklaim hasil ini sebagai skor IQ mutlak atau tolok ukur kecerdasan permanen.
- BAHASA KECENDERUNGAN: Selalu gunakan frasa berorientasi preferensi seperti "menunjukkan pola", "cenderung lebih nyaman", "berpotensi selaras", "pada saat asesmen berlangsung". Hindari determinisme mutlak.
- PRIVASI ZERO PII: Jangan meminta atau menyimpan identitas pribadi seperti nama asli, alamat, kontak, atau nomor identitas.
- RUJUKAN PROFESIONAL: Jika pengguna bertanya tentang masalah psikologis berat, stres akut, trauma, atau diagnosis klinis, dengan sopan nyatakan bahwa platform ini untuk self-insight non-klinis dan sarankan berkonsultasi dengan psikolog berlisensi atau layanan profesional terkait.

DATA HASIL ASESMEN PSIKOMETRI PENGGUNA (STRICTLY ANONYMIZED):
{$context}
EOT;
    }

    /**
     * Construct strictly sanitized psychometric summary without any PII.
     */
    public function buildSanitizedContext(AssessmentScore $score): string
    {
        $score->loadMissing([
            'session.assessment',
            'cognitiveScore',
            'personalityScore',
            'recommendations',
            'mlPrediction',
        ]);

        $lines = [];
        $lines[] = "- Tipe Asesmen: " . ucfirst($score->assessment_type);
        $lines[] = "- Judul Asesmen: " . ($score->session?->assessment?->title ?? 'Psikometri');
        $lines[] = "- Skor Keseluruhan: {$score->overall_score}%";
        $lines[] = "- Profil Arketipe: " . ($score->archetype_label ?? 'Seimbang');

        if ($score->cognitiveScore) {
            $c = $score->cognitiveScore;
            $lines[] = "- Skor Penalaran Logis: {$c->logical_score}%";
            $lines[] = "- Skor Penalaran Numerik: {$c->numerical_score}%";
            $lines[] = "- Skor Penalaran Verbal: {$c->verbal_score}%";
            $lines[] = "- Estimasi Persentil Normatif: ~{$c->percentile}";
        }

        if ($score->personalityScore) {
            $p = $score->personalityScore;
            $lines[] = "- Openness (Keterbukaan Ide): {$p->openness}%";
            $lines[] = "- Conscientiousness (Ketelitian & Kedisiplinan): {$p->conscientiousness}%";
            $lines[] = "- Extraversion (Interaksi Sosial): {$p->extraversion}%";
            $lines[] = "- Agreeableness (Empati & Keramahan): {$p->agreeableness}%";
            $lines[] = "- Neuroticism (Sensitivitas Responsif): {$p->neuroticism}%";
            $lines[] = "- Skor Konsistensi Jawaban: {$p->consistency_score}%";
        }

        if (!empty($score->strengths)) {
            $lines[] = "- Kekuatan Menonjol: " . implode('; ', $score->strengths);
        }

        if (!empty($score->development_areas)) {
            $lines[] = "- Area Pertumbuhan: " . implode('; ', $score->development_areas);
        }

        if ($score->mlPrediction) {
            $ml = $score->mlPrediction;
            $lines[] = "- Analisis ML Cluster: {$ml->cluster_label} (Cluster #{$ml->cluster_id})";
            $lines[] = "- Analisis ML Kategori: {$ml->predicted_category} (Keyakinan: {$ml->confidence}%)";
        }

        if ($score->recommendations->isNotEmpty()) {
            $recLines = [];
            foreach ($score->recommendations as $rec) {
                $areas = is_array($rec->suggested_areas) ? implode(', ', $rec->suggested_areas) : '';
                $recLines[] = "{$rec->title} (Saran Bidang: {$areas})";
            }
            $lines[] = "- Saran Eksplorasi: " . implode(' | ', $recLines);
        }

        return implode("\n", $lines);
    }
}
