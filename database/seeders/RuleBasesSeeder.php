<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuleBasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ruleBase = [
            // Dog diagnostic questions
            [
                'id' => 1,
                'target' => 'dog',
                'question' => 'Is your dog showing signs of lethargy or decreased activity?',
                'yes' => '2',
                'no' => '3',
                'is_first_question' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'target' => 'dog',
                'question' => 'Has your dog been eating less than usual or refusing food?',
                'yes' => 'Possible illness - Schedule immediate veterinary consultation',
                'no' => '4',
                'is_first_question' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'target' => 'dog',
                'question' => 'Is your dog showing any signs of pain (whimpering, limping, reluctance to move)?',
                'yes' => 'Pain management needed - Schedule veterinary examination',
                'no' => '5',
                'is_first_question' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'target' => 'dog',
                'question' => 'Does your dog have any digestive issues (vomiting, diarrhea)?',
                'yes' => 'Digestive concern - Monitor and schedule vet visit if symptoms persist',
                'no' => 'Monitor your dog and schedule routine check-up',
                'is_first_question' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 5,
                'target' => 'dog',
                'question' => 'Is your dog up to date with vaccinations?',
                'yes' => 'Great! Continue regular preventive care',
                'no' => 'Schedule vaccination appointment immediately',
                'is_first_question' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Cat diagnostic questions
            [
                'id' => 6,
                'target' => 'cat',
                'question' => 'Is your cat hiding more than usual or showing changes in behavior?',
                'yes' => '7',
                'no' => '8',
                'is_first_question' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 7,
                'target' => 'cat',
                'question' => 'Has your cat stopped using the litter box or showing changes in urination?',
                'yes' => 'Urinary issue suspected - Schedule immediate veterinary consultation',
                'no' => '9',
                'is_first_question' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 8,
                'target' => 'cat',
                'question' => 'Is your cat eating and drinking normally?',
                'yes' => '10',
                'no' => 'Appetite changes - Schedule veterinary examination',
                'is_first_question' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 9,
                'target' => 'cat',
                'question' => 'Does your cat show signs of respiratory issues (coughing, difficulty breathing)?',
                'yes' => 'Respiratory concern - Seek immediate veterinary care',
                'no' => 'Monitor behavior and schedule check-up if changes persist',
                'is_first_question' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 10,
                'target' => 'cat',
                'question' => 'Is your cat current on vaccinations and parasite prevention?',
                'yes' => 'Excellent! Maintain regular preventive care schedule',
                'no' => 'Schedule vaccination and parasite prevention appointment',
                'is_first_question' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('rule_bases')->insert($ruleBase);
    }
}
