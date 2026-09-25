<?php

namespace App\Services\AI;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AIProviderInterface
{
    protected ?string $apiKey;
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->baseUrl = rtrim(config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $this->model = config('services.gemini.model', 'gemini-3.6-flash');
    }

    public function getProviderName(): string
    {
        return 'gemini';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function sendMessage(array $messages, array $options = []): array
    {
        $errorNotice = null;

        // 1. If API Key is configured, attempt live call to Google Gemini API
        if ($this->isConfigured()) {
            try {
                // Separate system instruction from conversation history
                $systemTexts = [];
                $contents = [];

                foreach ($messages as $msg) {
                    $role = strtolower($msg['role'] ?? 'user');
                    $text = $msg['content'] ?? '';

                    if ($role === 'system') {
                        $systemTexts[] = $text;
                    } elseif ($role === 'assistant' || $role === 'model') {
                        $contents[] = [
                            'role' => 'model',
                            'parts' => [['text' => $text]],
                        ];
                    } else {
                        $contents[] = [
                            'role' => 'user',
                            'parts' => [['text' => $text]],
                        ];
                    }
                }

                $payload = [
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => $options['temperature'] ?? 0.7,
                        'maxOutputTokens' => $options['max_tokens'] ?? 4096,
                    ],
                ];

                if (!empty($systemTexts)) {
                    $payload['systemInstruction'] = [
                        'parts' => [['text' => implode("\n\n", $systemTexts)]],
                    ];
                }

                $targetModel = $options['model'] ?? $this->model;
                $url = "{$this->baseUrl}/models/{$targetModel}:generateContent?key={$this->apiKey}";

                $response = Http::timeout(45)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, tidak ada respons yang dihasilkan oleh model.';
                    $tokens = $data['usageMetadata']['totalTokenCount'] ?? 0;

                    // Clean plain text: strip markdown bold/italic asterisks (**) and headers (#)
                    $cleanContent = preg_replace('/\*\*(.*?)\*\*/', '$1', $content);
                    $cleanContent = preg_replace('/\*(.*?)\*/', '$1', $cleanContent);
                    $cleanContent = preg_replace('/^\s*#{1,6}\s+/m', '', $cleanContent);
                    $cleanContent = str_replace(['**', '***'], '', $cleanContent);
                    $cleanContent = trim($cleanContent);

                    return [
                        'content' => $cleanContent,
                        'model' => $targetModel,
                        'tokens_used' => $tokens,
                        'provider' => $this->getProviderName(),
                        'is_fallback' => false,
                    ];
                }

                $status = $response->status();
                $body = $response->json();
                $errMsg = $body['error']['message'] ?? '';

                Log::warning("Gemini API returned error status: {$status}", [
                    'body' => $response->body(),
                ]);

                if ($status === 400 || $status === 403) {
                    $errorNotice = "[Catatan: Google Gemini API mengembalikan HTTP {$status}" . ($errMsg ? " ({$errMsg})" : "") . ". Menampilkan analisis alternatif Mindora.]\n\n";
                } elseif ($status === 429) {
                    $errorNotice = "[Catatan: Batas kuota permintaan Gemini API terlampaui (HTTP 429 Rate Limit). Menampilkan analisis alternatif Mindora.]\n\n";
                } else {
                    $errorNotice = "[Catatan: Google Gemini API mengembalikan status HTTP {$status}. Menampilkan analisis alternatif Mindora.]\n\n";
                }
            } catch (Exception $e) {
                Log::error("Exception calling Gemini API: {$e->getMessage()}");
                $errorNotice = "[Catatan: Koneksi ke server Google Gemini terputus ({$e->getMessage()}). Menampilkan analisis alternatif Mindora.]\n\n";
            }
        }

        // 2. Intelligent Psychometric Fallback / Demonstration Engine
        return $this->generateContextualFallback($messages, $errorNotice);
    }

    /**
     * Generate constructive, empathetic, non-clinical demonstration responses
     * based on conversation context when API key is unconfigured or network is offline.
     */
    protected function generateContextualFallback(array $messages, ?string $customNotice = null): array
    {
        $lastUserMsg = '';
        foreach ($messages as $msg) {
            if (($msg['role'] ?? '') === 'user') {
                $lastUserMsg = strtolower($msg['content'] ?? '');
            }
        }

        $notice = $customNotice ?? "[Mindora Insight Engine — Mode Demonstrasi / Offline Aktif. Konfigurasikan GEMINI_API_KEY di .env untuk live model.]\n\n";

        if (str_contains($lastUserMsg, 'arti') || str_contains($lastUserMsg, 'skor') || str_contains($lastUserMsg, 'makna')) {
            $reply = "Berdasarkan pola asesmen yang telah Anda selesaikan, hasil Anda menggambarkan kecenderungan cara Anda memproses informasi dan berinteraksi dalam lingkungan kerja secara tipikal:\n\n" .
                "1. Kekuatan Utama: Profil Anda menunjukkan keunggulan pada kemampuan mengolah tugas terencana dan menjaga ritme kerja yang stabil.\n" .
                "2. Pola Respons: Waktu respons Anda tergolong konsisten, menunjukkan pertimbangan yang matang sebelum mengambil kesimpulan.\n" .
                "3. Refleksi Mandiri: Skor ini paling optimal dimaknai sebagai cermin preferensi kerja saat ini, bukan batasan mutlak kemampuan diri Anda.";
        } elseif (str_contains($lastUserMsg, 'tingkatkan') || str_contains($lastUserMsg, 'kembang') || str_contains($lastUserMsg, 'latih')) {
            $reply = "Untuk mengoptimalkan potensi dan mengembangkan aspek yang masih memiliki ruang pertumbuhan, berikut beberapa langkah praktis yang dapat dicoba:\n\n" .
                "- Praktik Reflektif Bertahap: Mulai dengan membagi tugas besar menjadi blok kerja 25-30 menit untuk mempertahankan fokus kognitif.\n" .
                "- Diversifikasi Perspektif: Latih keterbukaan terhadap ide baru dengan mendiskusikan topik di luar bidang kenyamanan Anda.\n" .
                "- Manajemen Energi: Imbangi tuntutan analitis dengan jeda istirahat teratur agar ketajaman persepsi tetap optimal.";
        } elseif (str_contains($lastUserMsg, 'karir') || str_contains($lastUserMsg, 'kerja') || str_contains($lastUserMsg, 'kolaborasi') || str_contains($lastUserMsg, 'peran')) {
            $reply = "Berdasarkan sinergi profil kognitif dan kepribadian Anda, berikut beberapa dinamika kerja yang berpotensi selaras:\n\n" .
                "- Gaya Kolaborasi: Anda cenderung memberikan dampak optimal dalam lingkungan yang menghargai kejelasan struktur, ekspektasi terdefinisi, dan komunikasi yang transparan.\n" .
                "- Peran Potensial: Bidang yang membutuhkan ketelitian analisis sistem, koordinasi proyek, atau pengembangan solusi berkelanjutan.\n" .
                "- Tips Sinergi Tim: Kolaborasikan kekuatan Anda dengan rekan yang memiliki orientasi eksplorasi dinamis untuk menghasilkan hasil kerja yang seimbang antara inovasi dan eksekusi.";
        } elseif (str_contains($lastUserMsg, 'machine learning') || str_contains($lastUserMsg, 'ml') || str_contains($lastUserMsg, 'shap') || str_contains($lastUserMsg, 'cluster')) {
            $reply = "Dalam model Machine Learning Mindora (K-Means & Random Forest), data Anda dianalisis melalui ruang 10 dimensi normatif:\n\n" .
                "- K-Means Clustering menempatkan profil Anda pada kluster perilaku yang memiliki kedekatan pola respon paling serasi.\n" .
                "- Penjelasan SHAP (Explainable AI) menunjukkan faktor dimensi yang paling dominan mendorong klasifikasi profil tersebut.\n" .
                "- Ini membantu kita memahami bukan hanya apa hasil akhirnya, tetapi juga mengapa sistem model mengelompokkan profil tersebut.";
        } else {
            $reply = "Terima kasih atas pertanyaannya. Sebagai Mindora Insight Assistant, saya siap membantu Anda mendalami setiap dimensi hasil asesmen psikometri ini secara konstruktif.\n\n" .
                "Anda dapat menanyakan hal-hal spesifik seperti:\n" .
                "- Makna dimensi kepribadian tertentu terhadap dinamika tim Anda.\n" .
                "- Strategi mengoptimalkan kebiasaan kerja harian.\n" .
                "- Rekomendasi eksplorasi peran atau metode pemecahan masalah yang selaras dengan profil kognitif Anda.\n\n" .
                "Catatan: Hasil ini dirancang sebagai panduan refleksi diri mandiri dan bukan diagnosis medis atau psikologis klinis.";
        }

        return [
            'content' => $notice . $reply,
            'model' => 'gemini-3.6-flash (offline-demo)',
            'tokens_used' => 150,
            'provider' => $this->getProviderName(),
            'is_fallback' => true,
        ];
    }
}
