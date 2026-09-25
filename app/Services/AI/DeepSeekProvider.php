<?php

namespace App\Services\AI;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepSeekProvider implements AIProviderInterface
{
    protected ?string $apiKey;
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.deepseek.api_key');
        $this->baseUrl = rtrim(config('services.deepseek.base_url', 'https://api.deepseek.com'), '/');
        $this->model = config('services.deepseek.model', 'deepseek-chat');
    }

    public function getProviderName(): string
    {
        return 'deepseek';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function sendMessage(array $messages, array $options = []): array
    {
        $errorNotice = null;

        // 1. If API Key is configured, attempt live call to DeepSeek API
        if ($this->isConfigured()) {
            try {
                $response = Http::timeout(25)
                    ->withToken($this->apiKey)
                    ->post("{$this->baseUrl}/chat/completions", [
                        'model' => $options['model'] ?? $this->model,
                        'messages' => $messages,
                        'temperature' => $options['temperature'] ?? 0.7,
                        'max_tokens' => $options['max_tokens'] ?? 1024,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $content = $data['choices'][0]['message']['content'] ?? 'Maaf, tidak ada respons yang dihasilkan oleh model.';
                    $tokens = $data['usage']['total_tokens'] ?? 0;

                    return [
                        'content' => $content,
                        'model' => $data['model'] ?? $this->model,
                        'tokens_used' => $tokens,
                        'provider' => $this->getProviderName(),
                        'is_fallback' => false,
                    ];
                }

                $status = $response->status();
                $body = $response->json();
                $errMsg = $body['error']['message'] ?? '';

                Log::warning("DeepSeek API returned error status: {$status}", [
                    'body' => $response->body(),
                ]);

                if ($status === 402 || str_contains(strtolower($errMsg), 'balance')) {
                    $errorNotice = "*[Catatan: API Key DeepSeek valid & terhubung, namun akun DeepSeek memiliki saldo \$0.00 (HTTP 402 Insufficient Balance). Harap lakukan top-up saldo di platform deepseek.com untuk live model. Menampilkan analisis cerdas Mindora Engine.]*\n\n";
                } elseif ($status === 401) {
                    $errorNotice = "*[Catatan: API Key DeepSeek tidak valid (HTTP 401 Unauthorized). Periksa konfigurasi DEEPSEEK_API_KEY di .env. Menampilkan analisis cerdas Mindora Engine.]*\n\n";
                } elseif ($status === 429) {
                    $errorNotice = "*[Catatan: Batas kuota permintaan DeepSeek API terlampaui (HTTP 429 Rate Limit). Menampilkan analisis cerdas Mindora Engine.]*\n\n";
                } else {
                    $errorNotice = "*[Catatan: DeepSeek API mengembalikan HTTP {$status}. Menampilkan analisis cerdas Mindora Engine.]*\n\n";
                }
            } catch (Exception $e) {
                Log::error("Exception calling DeepSeek API: {$e->getMessage()}");
                $errorNotice = "*[Catatan: Koneksi ke server DeepSeek terputus ({$e->getMessage()}). Menampilkan analisis cerdas Mindora Engine.]*\n\n";
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
        // Extract the last user message and system context
        $lastUserMsg = '';
        $systemContext = '';

        foreach ($messages as $msg) {
            if ($msg['role'] === 'user') {
                $lastUserMsg = strtolower($msg['content']);
            } elseif ($msg['role'] === 'system') {
                $systemContext = $msg['content'];
            }
        }

        $notice = $customNotice ?? "*[Mindora Insight Engine — Mode Demonstrasi / Offline Aktif. Konfigurasikan `DEEPSEEK_API_KEY` di .env untuk live model.]*\n\n";

        if (str_contains($lastUserMsg, 'arti') || str_contains($lastUserMsg, 'skor') || str_contains($lastUserMsg, 'makna')) {
            $reply = "Berdasarkan pola asesmen yang telah Anda selesaikan, hasil Anda menggambarkan kecenderungan cara Anda memproses informasi dan berinteraksi dalam lingkungan kerja secara tipikal:\n\n" .
                "1. **Kekuatan Utama**: Profil Anda menunjukkan keunggulan pada kemampuan mengolah tugas terencana dan menjaga ritme kerja yang stabil.\n" .
                "2. **Pola Respons**: Waktu respons Anda tergolong konsisten, menunjukkan pertimbangan yang matang sebelum mengambil kesimpulan.\n" .
                "3. **Refleksi Mandiri**: Skor ini paling optimal dimaknai sebagai cermin preferensi kerja saat ini, bukan batasan mutlak kemampuan diri Anda.";
        } elseif (str_contains($lastUserMsg, 'tingkatkan') || str_contains($lastUserMsg, 'kembang') || str_contains($lastUserMsg, 'latih')) {
            $reply = "Untuk mengoptimalkan potensi dan mengembangkan aspek yang masih memiliki ruang pertumbuhan, berikut beberapa langkah praktis yang dapat dicoba:\n\n" .
                "- **Praktik Reflektif Bertahap**: Mulai dengan membagi tugas besar menjadi blok kerja 25-30 menit untuk mempertahankan fokus kognitif.\n" .
                "- **Diversifikasi Perspektif**: Latih keterbukaan terhadap ide baru dengan mendiskusikan topik di luar bidang kenyamanan Anda.\n" .
                "- **Manajemen Energi**: Imbangi tuntutan analitis dengan jeda istirahat teratur agar ketajaman persepsi tetap optimal.";
        } elseif (str_contains($lastUserMsg, 'karir') || str_contains($lastUserMsg, 'kerja') || str_contains($lastUserMsg, 'kolaborasi') || str_contains($lastUserMsg, 'peran')) {
            $reply = "Berdasarkan sinergi profil kognitif dan kepribadian Anda, berikut beberapa dinamika kerja yang berpotensi selaras:\n\n" .
                "- **Gaya Kolaborasi**: Anda cenderung memberikan dampak optimal dalam lingkungan yang menghargai kejelasan struktur, ekspektasi terdefinisi, dan komunikasi yang transparan.\n" .
                "- **Peran Potensial**: Bidang yang membutuhkan ketelitian analisis sistem, koordinasi proyek, atau pengembangan solusi berkelanjutan.\n" .
                "- **Tips Sinergi Tim**: Kolaborasikan kekuatan Anda dengan rekan yang memiliki orientasi eksplorasi dinamis untuk menghasilkan hasil kerja yang seimbang antara inovasi dan eksekusi.";
        } elseif (str_contains($lastUserMsg, 'machine learning') || str_contains($lastUserMsg, 'ml') || str_contains($lastUserMsg, 'shap') || str_contains($lastUserMsg, 'cluster')) {
            $reply = "Dalam model Machine Learning Mindora (K-Means & Random Forest), data Anda dianalisis melalui ruang 10 dimensi normatif:\n\n" .
                "- **K-Means Clustering** menempatkan profil Anda pada kluster perilaku yang memiliki kedekatan pola respon paling serasi.\n" .
                "- **Penjelasan SHAP (Explainable AI)** menunjukkan faktor dimensi yang paling dominan mendorong klasifikasi profil tersebut.\n" .
                "- Ini membantu kita memahami bukan hanya *apa* hasil akhirnya, tetapi juga *mengapa* sistem model mengelompokkan profil tersebut.";
        } else {
            $reply = "Terima kasih atas pertanyaannya. Sebagai Mindora Insight Assistant, saya siap membantu Anda mendalami setiap dimensi hasil asesmen psikometri ini secara konstruktif.\n\n" .
                "Anda dapat menanyakan hal-hal spesifik seperti:\n" .
                "- Makna dimensi kepribadian tertentu terhadap dinamika tim Anda.\n" .
                "- Strategi mengoptimalkan kebiasaan kerja harian.\n" .
                "- Rekomendasi eksplorasi peran atau metode pemecahan masalah yang selaras dengan profil kognitif Anda.\n\n" .
                "*Catatan: Hasil ini dirancang sebagai panduan refleksi diri mandiri dan bukan diagnosis medis atau psikologis klinis.*";
        }

        return [
            'content' => $notice . $reply,
            'model' => 'deepseek-chat (offline-demo)',
            'tokens_used' => 150,
            'provider' => $this->getProviderName(),
            'is_fallback' => true,
        ];
    }
}
