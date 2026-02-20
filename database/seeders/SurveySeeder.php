<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionOption;
use Illuminate\Database\Seeder;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchantId = Merchant::first()?->id;

        $surveys = [
            [
                'title' => 'Rewarding Experiences; Share Your Thoughts',
                'description' => 'Influence your rewards journey! We value your insights. Share your thoughts on rewarding experiences, and unlock the door to earning big with Loyalify. Your feedback shapes the future of your rewards!',
                'points' => 750,
                'estimated_minutes' => 3,
                'merchant_id' => $merchantId,
                'questions' => [
                    [
                        'question_text' => 'Which type of rewards excites you the most?',
                        'options' => ['Discounts on purchases', 'Free products or services', 'Exclusive access to events'],
                    ],
                    [
                        'question_text' => 'How often do you participate in surveys?',
                        'options' => ['Very often', 'Sometimes', 'Rarely'],
                    ],
                ],
            ],
            [
                'title' => 'Loyalty Preferences; Your Ideal Rewards Awaits',
                'description' => 'Help us tailor rewards to your preferences. Tell us what matters most to you and earn points for your time.',
                'points' => 1500,
                'estimated_minutes' => 2,
                'merchant_id' => $merchantId,
                'questions' => [
                    [
                        'question_text' => 'What motivates you to complete a survey?',
                        'options' => ['Reward points', 'Helping improve services', 'Short completion time'],
                    ],
                    [
                        'question_text' => 'Preferred survey length?',
                        'options' => ['1-2 minutes', '3-5 minutes', '5+ minutes'],
                    ],
                ],
            ],
            [
                'title' => 'Shopping Habits Unleashed; Earn Points Today',
                'description' => 'Quick survey about your shopping habits. Your answers help us improve and you earn points instantly.',
                'points' => 1000,
                'estimated_minutes' => 1,
                'merchant_id' => $merchantId,
                'questions' => [
                    [
                        'question_text' => 'Where do you shop most often?',
                        'options' => ['Online', 'In-store', 'Both equally'],
                    ],
                ],
            ],
            [
                'title' => 'Merchant Magic – Which Brands Do You Love?',
                'description' => 'Tell us which brands and merchants you love. We use this to bring you better offers and rewards.',
                'points' => 2500,
                'estimated_minutes' => 5,
                'merchant_id' => $merchantId,
                'questions' => [
                    [
                        'question_text' => 'Which category do you spend the most on?',
                        'options' => ['Food & Dining', 'Retail & Shopping', 'Entertainment', 'Health & Beauty'],
                    ],
                    [
                        'question_text' => 'How important are loyalty programs when you choose where to shop?',
                        'options' => ['Very important', 'Somewhat important', 'Not very important'],
                    ],
                ],
            ],
            [
                'title' => 'Point Power; Tell Us Your Preferred Earning Method',
                'description' => 'How do you like to earn points? Spin, scan, or survey? Your input helps us design the best rewards experience.',
                'points' => 3500,
                'estimated_minutes' => 5,
                'merchant_id' => $merchantId,
                'questions' => [
                    [
                        'question_text' => 'Which earning method do you use most?',
                        'options' => ['Spin to Win', 'Scan QR codes', 'Surveys', 'All of them'],
                    ],
                    [
                        'question_text' => 'Would you recommend our rewards program to a friend?',
                        'options' => ['Yes, definitely', 'Probably', 'Not sure'],
                    ],
                ],
            ],
        ];

        foreach ($surveys as $data) {
            $questions = $data['questions'];
            unset($data['questions']);

            $survey = Survey::create(array_merge($data, ['status' => '1']));

            foreach ($questions as $sortOrder => $q) {
                $question = $survey->questions()->create([
                    'question_text' => $q['question_text'],
                    'sort_order' => $sortOrder + 1,
                ]);
                foreach (array_values($q['options']) as $optOrder => $optionText) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        'sort_order' => $optOrder + 1,
                    ]);
                }
            }
        }
    }
}
