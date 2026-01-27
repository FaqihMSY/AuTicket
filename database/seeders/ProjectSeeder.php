<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Auditor;
use App\Models\AssignmentType;
use App\Models\Department;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $assignmentTypes = AssignmentType::all();
        $departments = Department::all();

        $projectTemplates = [
            'Audit Rutin' => [
                'Pemeriksaan Laporan Keuangan Tahunan',
                'Audit Pengelolaan Anggaran Semester I',
            ],
            'Audit Khusus' => [
                'Pemeriksaan Khusus Pengadaan Barang',
                'Audit Investigasi Penggunaan Dana',
            ],
            'Investigasi' => [
                'Investigasi Dugaan Penyimpangan',
                'Pemeriksaan Kasus Pengadaan',
            ],
            'Audit Kepatuhan' => [
                'Pemeriksaan Kepatuhan Regulasi',
                'Audit Kepatuhan Standar Operasional',
            ],
            'Review Internal' => [
                'Review Prosedur Keuangan',
                'Evaluasi Sistem Pengendalian Internal',
            ],
            'Analisa Risiko' => [
                'Analisa Risiko Operasional',
                'Pemetaan Risiko Strategis',
            ],
            'Perencanaan Strategis' => [
                'Penyusunan Rencana Strategis 2026',
                'Evaluasi Pencapaian Target Strategis',
            ],
            'Review Kebijakan' => [
                'Review Kebijakan Pengadaan',
                'Evaluasi Kebijakan Keuangan',
            ],
            'Studi Kelayakan' => [
                'Studi Kelayakan Proyek Infrastruktur',
                'Analisa Kelayakan Program Baru',
            ],
            'Evaluasi Program' => [
                'Evaluasi Program Pembangunan',
                'Penilaian Efektivitas Program',
            ],
        ];

        foreach ($assignmentTypes as $assignmentType) {
            $templates = $projectTemplates[$assignmentType->name] ?? [
                'Pemeriksaan ' . $assignmentType->name . ' Periode I',
                'Evaluasi ' . $assignmentType->name . ' Periode II',
            ];

            foreach ($templates as $title) {
                $department = Department::find($assignmentType->department_id);

                $auditors = Auditor::all();

                if ($auditors->isEmpty()) {
                    continue;
                }

                $minAuditors = max(1, min(2, $auditors->count()));
                $maxAuditors = min(4, $auditors->count());
                $numAuditors = rand($minAuditors, $maxAuditors);

                $selectedAuditors = $auditors->count() === 1
                    ? $auditors
                    : $auditors->random($numAuditors);

                $startDate = now()->subDays(rand(30, 90));
                $endDate = $startDate->copy()->addDays(rand(30, 60));

                $project = Project::create([
                    'title' => $title,
                    'description' => 'Pelaksanaan ' . strtolower($assignmentType->name) . ' untuk memastikan kepatuhan terhadap standar dan regulasi yang berlaku serta meningkatkan efektivitas operasional.',
                    'assignment_type_id' => $assignmentType->id,
                    'department_id' => $department->id,
                    'created_by' => $department->users()->where('role', 'pengawas')->first()->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $this->getRandomStatus(),
                ]);

                $project->auditors()->attach($selectedAuditors->pluck('id'));

                if (in_array($project->status, ['ON_PROGRESS', 'WAITING', 'CLOSED'])) {
                    $project->published_at = $startDate;
                    $project->save();
                }

                if ($project->status === 'WAITING') {
                    $project->submitted_at = $startDate->copy()->addDays(rand(1, 5));
                    $project->save();
                }

                if ($project->status === 'CLOSED') {
                    $project->closed_at = $endDate->copy()->subDays(rand(0, 10));
                    $project->save();

                    // Generate reviews for each auditor
                    foreach ($project->auditors as $auditor) {
                        \App\Models\Review::create([
                            'project_id' => $project->id,
                            'reviewer_id' => $project->created_by,
                            'reviewee_id' => $auditor->user_id,
                            'overall_rating' => rand(70, 100),
                            'timeliness_rating' => rand(70, 100),
                            'completeness_rating' => rand(70, 100),
                            'quality_rating' => rand(70, 100),
                            'communication_rating' => rand(70, 100),
                            'feedback' => 'Performance review automatically generated by seeder.',
                            'created_at' => $project->closed_at,
                            'updated_at' => $project->closed_at,
                        ]);

                        $auditor->updatePerformanceScore();
                    }
                }
            }
        }
    }

    private function getRandomStatus(): string
    {
        $statuses = ['DRAFT', 'WAITING', 'ON_PROGRESS', 'CLOSED'];
        $weights = [10, 20, 40, 30];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $statuses[$index];
            }
        }

        return 'ON_PROGRESS';
    }
}
