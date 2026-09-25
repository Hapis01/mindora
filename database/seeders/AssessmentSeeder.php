<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentCategory;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $cognitiveCat = AssessmentCategory::firstOrCreate(
            ['code' => 'cognitive'],
            [
                'name' => 'Kemampuan Kognitif',
                'description' => 'Evaluasi penalaran logis, numerik, dan pemahaman verbal untuk mengukur profil kapasitas pemrosesan informasi.',
            ]
        );

        $personalityCat = AssessmentCategory::firstOrCreate(
            ['code' => 'personality'],
            [
                'name' => 'Kepribadian (Personality)',
                'description' => 'Eksplorasi dimensi kecenderungan perilaku berbasis Big Five Personality Model (O-C-E-A-N).',
            ]
        );

        // 2. Cognitive Assessment
        $cognitiveAssessment = Assessment::firstOrCreate(
            ['code' => 'cognitive-battery-v1'],
            [
                'category_id' => $cognitiveCat->id,
                'title' => 'Tes Kemampuan Kognitif (Cognitive Ability Battery)',
                'description' => 'Asesmen penalaran logis, analisis kuantitatif numerik, dan analogi verbal terstruktur.',
                'version' => 'v1.0',
                'status' => 'published',
                'duration_minutes' => 15,
                'instructions' => 'Pilihlah satu jawaban yang paling tepat untuk setiap pertanyaan. Setiap soal memiliki bobot skor yang sama. Waktu pengerjaan dan respons per soal akan dihitung otomatis oleh sistem.',
            ]
        );

        // Cognitive Questions Bank (Logical, Numerical, Verbal)
        $cogQuestions = [
            // Logical
            [
                'dimension' => 'logical',
                'text' => 'Semua analis sistem memahami logika algoritma. Sebagian desainer grafis adalah analis sistem. Berdasarkan dua premis tersebut, kesimpulan yang pasti benar adalah:',
                'difficulty' => 'medium',
                'options' => [
                    ['label' => 'A', 'text' => 'Semua desainer grafis memahami logika algoritma.', 'is_correct' => false],
                    ['label' => 'B', 'text' => 'Sebagian desainer grafis memahami logika algoritma.', 'is_correct' => true],
                    ['label' => 'C', 'text' => 'Tidak ada analis sistem yang bekerja sebagai desainer.', 'is_correct' => false],
                    ['label' => 'D', 'text' => 'Hanya analis sistem yang memahami algoritma.', 'is_correct' => false],
                ],
            ],
            [
                'dimension' => 'logical',
                'text' => 'Jika cuaca cerah maka server pendingin bekerja optimal. Jika server pendingin bekerja optimal maka tidak terjadi overheat pada database. Kenyataannya, terjadi overheat pada database. Kesimpulan yang valid adalah:',
                'difficulty' => 'medium',
                'options' => [
                    ['label' => 'A', 'text' => 'Cuaca tidak cerah.', 'is_correct' => true],
                    ['label' => 'B', 'text' => 'Server pendingin bekerja optimal.', 'is_correct' => false],
                    ['label' => 'C', 'text' => 'Cuaca sangat cerah.', 'is_correct' => false],
                    ['label' => 'D', 'text' => 'Database tidak mengalami kerusakan.', 'is_correct' => false],
                ],
            ],
            [
                'dimension' => 'logical',
                'text' => 'Urutan langkah pengujian modul perangkat lunak: Modul A harus diuji sebelum Modul B. Modul C diuji setelah Modul B tetapi sebelum Modul D. Modul E diuji paling terakhir. Modul manakah yang berada pada urutan pengujian ketiga?',
                'difficulty' => 'easy',
                'options' => [
                    ['label' => 'A', 'text' => 'Modul A', 'is_correct' => false],
                    ['label' => 'B', 'text' => 'Modul B', 'is_correct' => false],
                    ['label' => 'C', 'text' => 'Modul C', 'is_correct' => true],
                    ['label' => 'D', 'text' => 'Modul D', 'is_correct' => false],
                ],
            ],

            // Numerical
            [
                'dimension' => 'numerical',
                'text' => 'Perhatikan deret bilangan berikut: 3, 7, 15, 31, 63, ... Bilangan selanjutnya pada pola deret ini adalah:',
                'difficulty' => 'medium',
                'options' => [
                    ['label' => 'A', 'text' => '125', 'is_correct' => false],
                    ['label' => 'B', 'text' => '127', 'is_correct' => true],
                    ['label' => 'C', 'text' => '129', 'is_correct' => false],
                    ['label' => 'D', 'text' => '135', 'is_correct' => false],
                ],
            ],
            [
                'dimension' => 'numerical',
                'text' => 'Sebuah server cloud memproses 450 transaksi dalam 15 detik. Dengan kecepatan konstan yang sama, berapa detik yang dibutuhkan server untuk menyelesaikan 1.800 transaksi?',
                'difficulty' => 'easy',
                'options' => [
                    ['label' => 'A', 'text' => '45 detik', 'is_correct' => false],
                    ['label' => 'B', 'text' => '60 detik', 'is_correct' => true],
                    ['label' => 'C', 'text' => '75 detik', 'is_correct' => false],
                    ['label' => 'D', 'text' => '90 detik', 'is_correct' => false],
                ],
            ],
            [
                'dimension' => 'numerical',
                'text' => 'Pendapatan sebuah startup teknologi naik 25% pada kuartal pertama menjadi Rp 250.000.000. Berapakah pendapatan startup tersebut sebelum mengalami kenaikan?',
                'difficulty' => 'medium',
                'options' => [
                    ['label' => 'A', 'text' => 'Rp 180.000.000', 'is_correct' => false],
                    ['label' => 'B', 'text' => 'Rp 200.000.000', 'is_correct' => true],
                    ['label' => 'C', 'text' => 'Rp 210.000.000', 'is_correct' => false],
                    ['label' => 'D', 'text' => 'Rp 225.000.000', 'is_correct' => false],
                ],
            ],

            // Verbal
            [
                'dimension' => 'verbal',
                'text' => 'TELESKOP : BINTANG = MIKROSKOP : ...',
                'difficulty' => 'easy',
                'options' => [
                    ['label' => 'A', 'text' => 'Bakteri / Mikroba', 'is_correct' => true],
                    ['label' => 'B', 'text' => 'Lensa', 'is_correct' => false],
                    ['label' => 'C', 'text' => 'Laboratorium', 'is_correct' => false],
                    ['label' => 'D', 'text' => 'Cahaya', 'is_correct' => false],
                ],
            ],
            [
                'dimension' => 'verbal',
                'text' => 'Pilihlah pasangan kata yang memiliki relasi analogi yang paling setara dengan ALGORITMA : PROGRAMMER:',
                'difficulty' => 'medium',
                'options' => [
                    ['label' => 'A', 'text' => 'Cetaka Biru (Blueprint) : Arsitek', 'is_correct' => true],
                    ['label' => 'B', 'text' => 'Cat : Lukisan', 'is_correct' => false],
                    ['label' => 'C', 'text' => 'Palu : Tukang Kayu', 'is_correct' => false],
                    ['label' => 'D', 'text' => 'Buku : Pembaca', 'is_correct' => false],
                ],
            ],
            [
                'dimension' => 'verbal',
                'text' => 'Sinonim kontekstual yang paling tepat untuk kata "KOMPREHENSIF" dalam kalimat "Laporan evaluasi psikometri disajikan secara komprehensif" adalah:',
                'difficulty' => 'medium',
                'options' => [
                    ['label' => 'A', 'text' => 'Menyeluruh dan luas', 'is_correct' => true],
                    ['label' => 'B', 'text' => 'Singkat dan padat', 'is_correct' => false],
                    ['label' => 'C', 'text' => 'Rumit dan terbelit', 'is_correct' => false],
                    ['label' => 'D', 'text' => 'Sementara', 'is_correct' => false],
                ],
            ],
        ];

        foreach ($cogQuestions as $idx => $qData) {
            $question = Question::firstOrCreate(
                [
                    'assessment_id' => $cognitiveAssessment->id,
                    'order' => $idx + 1,
                ],
                [
                    'dimension' => $qData['dimension'],
                    'question_text' => $qData['text'],
                    'question_type' => 'multiple_choice',
                    'weight' => 1,
                    'difficulty' => $qData['difficulty'],
                    'is_reverse_scored' => false,
                    'status' => 'published',
                ]
            );

            foreach ($qData['options'] as $oIdx => $opt) {
                QuestionOption::firstOrCreate(
                    [
                        'question_id' => $question->id,
                        'label' => $opt['label'],
                    ],
                    [
                        'option_text' => $opt['text'],
                        'score_value' => $opt['is_correct'] ? 1 : 0,
                        'is_correct' => $opt['is_correct'],
                        'order' => $oIdx + 1,
                    ]
                );
            }
        }

        // 3. Personality Assessment (Big Five)
        $personalityAssessment = Assessment::firstOrCreate(
            ['code' => 'big-five-v1'],
            [
                'category_id' => $personalityCat->id,
                'title' => 'Tes Kepribadian Big Five (OCEAN Inventory)',
                'description' => 'Evaluasi psikometrik lima dimensi kepribadian: Openness, Conscientiousness, Extraversion, Agreeableness, dan Neuroticism.',
                'version' => 'v1.0',
                'status' => 'published',
                'duration_minutes' => 10,
                'instructions' => 'Nyatakan seberapa jauh setiap pernyataan berikut menggambarkan kecenderungan diri Anda secara jujur dan apa adanya menggunakan skala 1 (Sangat Tidak Setuju) hingga 5 (Sangat Setuju). Tidak ada jawaban benar atau salah.',
            ]
        );

        $likertOptions = [
            ['label' => '1', 'text' => 'Sangat Tidak Setuju', 'score_value' => 1],
            ['label' => '2', 'text' => 'Tidak Setuju', 'score_value' => 2],
            ['label' => '3', 'text' => 'Netral / Ragu-ragu', 'score_value' => 3],
            ['label' => '4', 'text' => 'Setuju', 'score_value' => 4],
            ['label' => '5', 'text' => 'Sangat Setuju', 'score_value' => 5],
        ];

        $personalityQuestions = [
            // Openness
            [
                'dimension' => 'openness',
                'text' => 'Saya senang mengeksplorasi ide-ide baru yang abstrak dan menantang sudut pandang pemikiran saya.',
                'is_reverse_scored' => false,
            ],
            [
                'dimension' => 'openness',
                'text' => 'Saya lebih menyukai cara kerja rutin konvensional daripada mencoba metode baru yang belum teruji.',
                'is_reverse_scored' => true,
            ],

            // Conscientiousness
            [
                'dimension' => 'conscientiousness',
                'text' => 'Saya selalu merencanakan tugas dengan teratur dan konsisten menuntaskan pekerjaan sesuai tenggat waktu.',
                'is_reverse_scored' => false,
            ],
            [
                'dimension' => 'conscientiousness',
                'text' => 'Saya sering menunda-nunda pekerjaan penting hingga batas waktu akhir mendekat.',
                'is_reverse_scored' => true,
            ],

            // Extraversion
            [
                'dimension' => 'extraversion',
                'text' => 'Saya merasa bersemangat dan berenergi ketika berinteraksi serta bertukar pikiran dalam kelompok ramai.',
                'is_reverse_scored' => false,
            ],
            [
                'dimension' => 'extraversion',
                'text' => 'Saya cenderung cepat merasa lelah dan butuh waktu menyendiri setelah berada di forum sosial yang besar.',
                'is_reverse_scored' => true,
            ],

            // Agreeableness
            [
                'dimension' => 'agreeableness',
                'text' => 'Saya secara tulus peduli terhadap perasaan rekan tim dan berusaha menciptakan suasana kerja yang harmonis.',
                'is_reverse_scored' => false,
            ],
            [
                'dimension' => 'agreeableness',
                'text' => 'Saya cenderung skeptis terhadap motivasi orang lain dan lebih fokus mengejar hasil tanpa peduli perasaan orang lain.',
                'is_reverse_scored' => true,
            ],

            // Neuroticism
            [
                'dimension' => 'neuroticism',
                'text' => 'Saya mudah merasa khawatir atau cemas saat menghadapi perubahan situasi mendadak yang tidak menentu.',
                'is_reverse_scored' => false,
            ],
            [
                'dimension' => 'neuroticism',
                'text' => 'Saya tetap tenang, stabil secara emosional, dan mampu berkepala dingin dalam situasi penuh tekanan tinggi.',
                'is_reverse_scored' => true,
            ],
        ];

        foreach ($personalityQuestions as $idx => $pq) {
            $question = Question::firstOrCreate(
                [
                    'assessment_id' => $personalityAssessment->id,
                    'order' => $idx + 1,
                ],
                [
                    'dimension' => $pq['dimension'],
                    'question_text' => $pq['text'],
                    'question_type' => 'likert',
                    'weight' => 1,
                    'difficulty' => 'medium',
                    'is_reverse_scored' => $pq['is_reverse_scored'],
                    'status' => 'published',
                ]
            );

            foreach ($likertOptions as $opt) {
                QuestionOption::firstOrCreate(
                    [
                        'question_id' => $question->id,
                        'label' => $opt['label'],
                    ],
                    [
                        'option_text' => $opt['text'],
                        'score_value' => $opt['score_value'],
                        'is_correct' => false,
                        'order' => $opt['score_value'],
                    ]
                );
            }
        }
    }
}
