<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;
use App\Models\MindfulnessContent;

class FeatureSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Exercises
        $exercises = [
            [
                'name' => 'Barbell Squat',
                'category' => 'Strength',
                'muscle_group' => 'Legs',
                'equipment' => 'Barbell',
                'difficulty' => 'Intermediate',
                'instructions' => 'Place barbell on upper back. Squat down until thighs are parallel to floor. Stand back up.',
                'tips' => 'Keep your back straight and weight on your heels.',
                'benefits' => 'Builds leg strength, improves core stability, and increases bone density.',
                'precautions' => 'Avoid if you have acute back or knee injuries. Maintain proper spinal alignment.',
                'calories_per_hour' => 400
            ],
            [
                'name' => 'Pushups',
                'category' => 'Strength',
                'muscle_group' => 'Chest',
                'equipment' => 'Bodyweight',
                'difficulty' => 'Beginner',
                'instructions' => 'Start in plank position. Lower body until chest nearly touches floor. Push back up.',
                'tips' => 'Engage your core to keep body straight.',
                'calories_per_hour' => 300
            ],
            [
                'name' => 'Deadlift',
                'category' => 'Strength',
                'muscle_group' => 'Back',
                'equipment' => 'Barbell',
                'difficulty' => 'Advanced',
                'instructions' => 'Stand with feet hip-width apart. Grip barbell. Lift by extending hips and knees.',
                'tips' => 'Keep the bar close to your shins.',
                'calories_per_hour' => 500
            ],
            [
                'name' => 'Plank',
                'category' => 'Core',
                'muscle_group' => 'Abs',
                'equipment' => 'Bodyweight',
                'difficulty' => 'Beginner',
                'instructions' => 'Hold a pushup-like position but on your forearms.',
                'tips' => 'Do not let your hips sag.',
                'calories_per_hour' => 150
            ]
        ];
        
        foreach($exercises as $ex) {
            Exercise::updateOrCreate(['name' => $ex['name']], $ex);
        }

        // 2. Seed Mindfulness
        $mindfulness = [
            [
                'title' => 'Morning Zen Meditation',
                'category' => 'Meditation',
                'description' => 'A calm start to your day with guided breathing.',
                'content' => "Welcome to your morning zen session.\n\n1. Find a quiet place where you won't be disturbed.\n2. Sit comfortably with your spine straight.\n3. Close your eyes and focus on your natural breath.\n4. If your mind wanders, gently bring it back to your breath.",
                'duration_minutes' => 10,
                'media_url' => 'https://www.youtube.com/watch?v=inpok4MKVLM',
                'image_url' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&q=80&w=800'
            ],
            [
                'title' => 'Deep Tissue Recovery',
                'category' => 'Recovery',
                'description' => 'Guided stretching for major muscle groups.',
                'content' => "Effective recovery is key to muscle growth.\n\n- Hold each stretch for 30 seconds.\n- Focus on the muscle being stretched.\n- Do not bounce; keep the tension steady.",
                'duration_minutes' => 15,
                'media_url' => 'https://www.youtube.com/watch?v=g_tea8ZNk5A',
                'image_url' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&q=80&w=800'
            ],
            [
                'title' => 'Box Breathing for Stress',
                'category' => 'Breathing',
                'description' => 'A simple technique to calm the nervous system.',
                'content' => "Box breathing is used by athletes and Navy SEALs to stay calm under pressure.\n\n- Inhale for 4 seconds.\n- Hold for 4 seconds.\n- Exhale for 4 seconds.\n- Hold for 4 seconds.\n- Repeat 4 times.",
                'duration_minutes' => 5,
                'media_url' => 'https://www.youtube.com/watch?v=aNXKjGFUlMs',
                'image_url' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&q=80&w=800'
            ]
        ];
        
        foreach($mindfulness as $m) {
            MindfulnessContent::updateOrCreate(['title' => $m['title']], $m);
        }
    }
}
