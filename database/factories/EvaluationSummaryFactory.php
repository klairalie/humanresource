<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employeeprofiles;
use App\Models\Assessment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EvaluationSummary>
 */
class EvaluationSummaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Possible categories (same as in your evaluation logic)
        $categories = [
            'Work Performance & Skills',
            'Teamwork & Collaboration',
            'Professional Behavior',
            'Safety & Responsibility',
        ];

        // Generate fake evaluator & evaluatee
        $evaluator = Employeeprofiles::inRandomOrder()->first() ?? Employeeprofiles::factory()->create();
        $evaluatee = Employeeprofiles::inRandomOrder()->first() ?? Employeeprofiles::factory()->create();

        // Ensure evaluator != evaluatee
        while ($evaluatee->employeeprofiles_id === $evaluator->employeeprofiles_id) {
            $evaluatee = Employeeprofiles::inRandomOrder()->first() ?? Employeeprofiles::factory()->create();
        }

        // Fake category ratings (1–5 scale, 10 questions per category)
        $categoryScores = [];
        foreach ($categories as $cat) {
            $ratings = collect(range(1, 10))->map(fn() => fake()->numberBetween(1, 5));
            $categoryScores[$cat] = round($ratings->avg(), 2); // average rating for that category
        }

        // Compute total_score (scale to 200 max → 4 categories × 10 questions × 5 max = 200)
        $totalScore = array_sum(array_map(fn($score) => $score * 10, $categoryScores));

        return [
            'evaluator_id'    => $evaluator->employeeprofiles_id,
            'evaluatee_id'    => $evaluatee->employeeprofiles_id,
            'assessment_id'   => Assessment::inRandomOrder()->value('assessment_id') ?? Assessment::factory()->create()->assessment_id,
            'total_score'     => $totalScore,
            'category_scores' => json_encode($categoryScores, JSON_UNESCAPED_UNICODE),
            'feedback'        => fake()->optional()->sentence(12),
            'created_at'      => fake()->dateTimeBetween('-6 months', 'now'),
            'updated_at'      => now(),
        ];
    }
}
