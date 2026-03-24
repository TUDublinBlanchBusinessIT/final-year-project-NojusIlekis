<?php

namespace Database\Seeders;

use App\Models\Milestone;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    public function run(): void
    {
        $milestones = [

            // -----------------------------------------------
            // Well-being
            // -----------------------------------------------
            ['name' => 'Responds to comfort from familiar adults',  'description' => 'Child calms when held or soothed by a known carer or family member.',                          'category' => 'wellbeing', 'age_range_months' => '0-12',  'sort_order' => 1],
            ['name' => 'Reaches for objects',                        'description' => 'Child intentionally stretches out a hand to grasp a toy or item within reach.',               'category' => 'wellbeing', 'age_range_months' => '0-12',  'sort_order' => 2],
            ['name' => 'Begins to self-feed finger foods',           'description' => 'Child picks up small pieces of soft food and brings them to their mouth independently.',       'category' => 'wellbeing', 'age_range_months' => '0-12',  'sort_order' => 3],

            ['name' => 'Walks independently',                        'description' => 'Child can walk without holding onto furniture or an adult.',                                   'category' => 'wellbeing', 'age_range_months' => '12-24', 'sort_order' => 1],
            ['name' => 'Drinks from a cup',                          'description' => 'Child holds and drinks from an open cup or beaker with minimal spilling.',                    'category' => 'wellbeing', 'age_range_months' => '12-24', 'sort_order' => 2],
            ['name' => 'Shows awareness of danger',                  'description' => 'Child hesitates or looks to an adult before touching something hot or approaching a step.',   'category' => 'wellbeing', 'age_range_months' => '12-24', 'sort_order' => 3],

            ['name' => 'Can hold a spoon and feed self',             'description' => 'Child uses a spoon to scoop food and deliver it to their mouth with reasonable accuracy.',    'category' => 'wellbeing', 'age_range_months' => '24-36', 'sort_order' => 1],
            ['name' => 'Climbs stairs with support',                 'description' => 'Child goes up and down steps while holding a rail or an adult\'s hand.',                     'category' => 'wellbeing', 'age_range_months' => '24-36', 'sort_order' => 2],
            ['name' => 'Shows interest in toilet training',          'description' => 'Child shows awareness of needing to use the toilet and may ask or signal to a carer.',       'category' => 'wellbeing', 'age_range_months' => '24-36', 'sort_order' => 3],

            ['name' => 'Dresses with some help',                     'description' => 'Child can put on and remove simple items of clothing with minimal adult assistance.',         'category' => 'wellbeing', 'age_range_months' => '36-48', 'sort_order' => 1],
            ['name' => 'Hops on one foot',                           'description' => 'Child can balance on one foot and hop at least twice without support.',                       'category' => 'wellbeing', 'age_range_months' => '36-48', 'sort_order' => 2],
            ['name' => 'Manages bathroom needs mostly independently', 'description' => 'Child uses the toilet with little or no adult prompting and can manage clothing.',           'category' => 'wellbeing', 'age_range_months' => '36-48', 'sort_order' => 3],

            ['name' => 'Ties shoelaces with help',                   'description' => 'Child attempts the steps of tying a bow and can complete with minimal adult guidance.',      'category' => 'wellbeing', 'age_range_months' => '48-60', 'sort_order' => 1],
            ['name' => 'Rides a tricycle confidently',               'description' => 'Child pedals, steers, and stops a tricycle safely in an open space.',                         'category' => 'wellbeing', 'age_range_months' => '48-60', 'sort_order' => 2],
            ['name' => 'Shows good hand-eye coordination',           'description' => 'Child can catch a large ball, thread beads, or complete tasks requiring precise hand movements.', 'category' => 'wellbeing', 'age_range_months' => '48-60', 'sort_order' => 3],

            // -----------------------------------------------
            // Identity & Belonging
            // -----------------------------------------------
            ['name' => 'Recognises primary carers',                  'description' => 'Child shows recognition of familiar adults through smiling, reaching, or calming.',           'category' => 'identity-belonging', 'age_range_months' => '0-12',  'sort_order' => 1],
            ['name' => 'Smiles in response to faces',                'description' => 'Child produces a social smile when a familiar person makes eye contact and smiles.',          'category' => 'identity-belonging', 'age_range_months' => '0-12',  'sort_order' => 2],
            ['name' => 'Shows attachment behaviours',                'description' => 'Child protests separation from a key carer and seeks closeness when distressed.',             'category' => 'identity-belonging', 'age_range_months' => '0-12',  'sort_order' => 3],

            ['name' => 'Uses own name',                              'description' => 'Child responds to and begins to use their own name in play or conversation.',                 'category' => 'identity-belonging', 'age_range_months' => '12-24', 'sort_order' => 1],
            ['name' => 'Shows preference for certain toys',          'description' => 'Child consistently reaches for or requests specific toys or activities they enjoy.',          'category' => 'identity-belonging', 'age_range_months' => '12-24', 'sort_order' => 2],
            ['name' => 'Plays alongside other children',             'description' => 'Child engages in parallel play, playing near peers and observing their actions.',             'category' => 'identity-belonging', 'age_range_months' => '12-24', 'sort_order' => 3],

            ['name' => 'Says "mine" showing sense of ownership',     'description' => 'Child asserts ownership of belongings and begins to understand personal property.',           'category' => 'identity-belonging', 'age_range_months' => '24-36', 'sort_order' => 1],
            ['name' => 'Identifies self in mirror',                  'description' => 'Child points to or names themselves when looking in a mirror or at a photo.',                 'category' => 'identity-belonging', 'age_range_months' => '24-36', 'sort_order' => 2],
            ['name' => 'Shows empathy for others feelings',          'description' => 'Child notices when another child is upset and may offer comfort such as a toy or a hug.',    'category' => 'identity-belonging', 'age_range_months' => '24-36', 'sort_order' => 3],

            ['name' => 'Can describe themselves',                    'description' => 'Child can state simple facts about themselves such as name, age, or hair colour.',            'category' => 'identity-belonging', 'age_range_months' => '36-48', 'sort_order' => 1],
            ['name' => 'Makes friends',                              'description' => 'Child seeks out the same peers repeatedly and shows preference for playing with them.',        'category' => 'identity-belonging', 'age_range_months' => '36-48', 'sort_order' => 2],
            ['name' => 'Takes turns in simple games',                'description' => 'Child waits for their turn during structured games without significant adult prompting.',     'category' => 'identity-belonging', 'age_range_months' => '36-48', 'sort_order' => 3],

            ['name' => 'Shows pride in achievements',                'description' => 'Child seeks adult acknowledgement when completing a task and expresses pleasure at success.', 'category' => 'identity-belonging', 'age_range_months' => '48-60', 'sort_order' => 1],
            ['name' => 'Understands family roles',                   'description' => 'Child can name and describe the roles of different family members in conversation or play.',  'category' => 'identity-belonging', 'age_range_months' => '48-60', 'sort_order' => 2],
            ['name' => 'Cooperates in group activities',             'description' => 'Child participates in group tasks, follows shared rules, and supports peers.',                'category' => 'identity-belonging', 'age_range_months' => '48-60', 'sort_order' => 3],

            // -----------------------------------------------
            // Communicating
            // -----------------------------------------------
            ['name' => 'Babbles with expression',                    'description' => 'Child produces strings of sounds with varied pitch and tone that mimic conversation.',        'category' => 'communicating', 'age_range_months' => '0-12',  'sort_order' => 1],
            ['name' => 'Responds to own name',                       'description' => 'Child turns head or looks up when their name is called from across a room.',                  'category' => 'communicating', 'age_range_months' => '0-12',  'sort_order' => 2],
            ['name' => 'Points to desired objects',                  'description' => 'Child extends an index finger toward something they want or find interesting.',               'category' => 'communicating', 'age_range_months' => '0-12',  'sort_order' => 3],

            ['name' => 'Says 5 or more recognisable words',          'description' => 'Child uses at least five distinct words consistently to refer to people or objects.',         'category' => 'communicating', 'age_range_months' => '12-24', 'sort_order' => 1],
            ['name' => 'Follows simple instructions',                'description' => 'Child can carry out a one-step instruction such as "get your shoes" without a gesture cue.',  'category' => 'communicating', 'age_range_months' => '12-24', 'sort_order' => 2],
            ['name' => 'Uses gestures to communicate',               'description' => 'Child waves goodbye, shakes head for no, and nods for yes in appropriate contexts.',          'category' => 'communicating', 'age_range_months' => '12-24', 'sort_order' => 3],

            ['name' => 'Uses 2-3 word phrases',                      'description' => 'Child combines words to form simple phrases such as "more juice" or "daddy go".',            'category' => 'communicating', 'age_range_months' => '24-36', 'sort_order' => 1],
            ['name' => 'Knows some colours',                         'description' => 'Child can correctly name at least two colours when shown objects.',                           'category' => 'communicating', 'age_range_months' => '24-36', 'sort_order' => 2],
            ['name' => 'Can say own name clearly',                   'description' => 'Child states their own first name clearly enough for an unfamiliar adult to understand.',     'category' => 'communicating', 'age_range_months' => '24-36', 'sort_order' => 3],

            ['name' => 'Speaks in full sentences',                   'description' => 'Child uses sentences of four or more words to express ideas and make requests.',              'category' => 'communicating', 'age_range_months' => '36-48', 'sort_order' => 1],
            ['name' => 'Asks why questions',                         'description' => 'Child regularly asks "why" to seek explanations about the world around them.',               'category' => 'communicating', 'age_range_months' => '36-48', 'sort_order' => 2],
            ['name' => 'Can retell a simple story',                  'description' => 'Child recounts the main events of a familiar story or recent experience in sequence.',        'category' => 'communicating', 'age_range_months' => '36-48', 'sort_order' => 3],

            ['name' => 'Uses past tense correctly',                  'description' => 'Child correctly forms the past tense of common verbs in everyday conversation.',              'category' => 'communicating', 'age_range_months' => '48-60', 'sort_order' => 1],
            ['name' => 'Recognises some letters',                    'description' => 'Child can name or identify at least some letters of the alphabet, often starting with their own name.', 'category' => 'communicating', 'age_range_months' => '48-60', 'sort_order' => 2],
            ['name' => 'Enjoys rhymes and songs',                    'description' => 'Child actively participates in rhymes and songs, recalling words and joining in with enthusiasm.', 'category' => 'communicating', 'age_range_months' => '48-60', 'sort_order' => 3],

            // -----------------------------------------------
            // Exploring & Thinking
            // -----------------------------------------------
            ['name' => 'Explores objects by mouthing',               'description' => 'Child places toys and objects in their mouth as a way to explore texture and form.',         'category' => 'exploring-thinking', 'age_range_months' => '0-12',  'sort_order' => 1],
            ['name' => 'Tracks moving objects with eyes',            'description' => 'Child follows a slowly moving object from side to side with their gaze.',                    'category' => 'exploring-thinking', 'age_range_months' => '0-12',  'sort_order' => 2],
            ['name' => 'Drops and picks up toys repeatedly',         'description' => 'Child intentionally drops items and looks for them, exploring cause and effect.',             'category' => 'exploring-thinking', 'age_range_months' => '0-12',  'sort_order' => 3],

            ['name' => 'Stacks 2-3 blocks',                          'description' => 'Child places one block on top of another to build a small tower of two or three.',           'category' => 'exploring-thinking', 'age_range_months' => '12-24', 'sort_order' => 1],
            ['name' => 'Enjoys simple puzzles',                      'description' => 'Child attempts and completes a basic shape-sorter or two-piece puzzle.',                      'category' => 'exploring-thinking', 'age_range_months' => '12-24', 'sort_order' => 2],
            ['name' => 'Explores cause and effect',                  'description' => 'Child repeatedly presses buttons, bangs objects, or fills and empties containers to see what happens.', 'category' => 'exploring-thinking', 'age_range_months' => '12-24', 'sort_order' => 3],

            ['name' => 'Sorts objects by colour or shape',           'description' => 'Child groups items together based on a shared attribute such as colour or shape.',            'category' => 'exploring-thinking', 'age_range_months' => '24-36', 'sort_order' => 1],
            ['name' => 'Counts to 3',                                'description' => 'Child says the number sequence 1, 2, 3 in order and may match numbers to objects.',          'category' => 'exploring-thinking', 'age_range_months' => '24-36', 'sort_order' => 2],
            ['name' => 'Engages in pretend play',                    'description' => 'Child acts out simple scenarios such as feeding a doll or pretending to cook.',              'category' => 'exploring-thinking', 'age_range_months' => '24-36', 'sort_order' => 3],

            ['name' => 'Counts to 10',                               'description' => 'Child recites numbers 1 through 10 in the correct order and begins to count objects.',       'category' => 'exploring-thinking', 'age_range_months' => '36-48', 'sort_order' => 1],
            ['name' => 'Draws recognisable shapes',                  'description' => 'Child produces drawings that clearly represent circles, squares, or simple figures.',         'category' => 'exploring-thinking', 'age_range_months' => '36-48', 'sort_order' => 2],
            ['name' => 'Understands same and different',             'description' => 'Child correctly identifies matching pairs and can explain how two objects are alike or different.', 'category' => 'exploring-thinking', 'age_range_months' => '36-48', 'sort_order' => 3],

            ['name' => 'Writes own name',                            'description' => 'Child can write the letters of their first name in recognisable form.',                       'category' => 'exploring-thinking', 'age_range_months' => '48-60', 'sort_order' => 1],
            ['name' => 'Understands time concepts like yesterday and tomorrow', 'description' => 'Child uses words like yesterday, today, and tomorrow accurately in conversation.',  'category' => 'exploring-thinking', 'age_range_months' => '48-60', 'sort_order' => 2],
            ['name' => 'Solves simple problems independently',       'description' => 'Child works through a straightforward problem such as fitting shapes or navigating an obstacle without adult help.', 'category' => 'exploring-thinking', 'age_range_months' => '48-60', 'sort_order' => 3],
        ];

        foreach ($milestones as $data) {
            Milestone::updateOrCreate(
                [
                    'name'             => $data['name'],
                    'category'         => $data['category'],
                    'age_range_months' => $data['age_range_months'],
                ],
                [
                    'description' => $data['description'],
                    'sort_order'  => $data['sort_order'],
                ]
            );
        }
    }
}
