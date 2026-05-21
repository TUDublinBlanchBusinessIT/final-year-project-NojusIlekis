<?php

namespace Database\Seeders;

use App\Models\Acknowledgement;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\DailyReport;
use App\Models\DailyUpdate;
use App\Models\IncidentReport;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MedicationLog;
use App\Models\Message;
use App\Models\Milestone;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------------------------------------------------
        // 1. Refresh display names on the demo accounts so the UI looks realistic
        // -----------------------------------------------------------------------
        $manager = User::updateOrCreate(
            ['email' => 'manager@test.com'],
            [
                'name'              => 'Mary Collins',
                'password'          => Hash::make('Password123!'),
                'role'              => 'manager',
                'email_verified_at' => now(),
                'status'            => 'approved',
                'approved_at'       => now(),
            ]
        );

        $carer = User::updateOrCreate(
            ['email' => 'carer@test.com'],
            [
                'name'              => 'Sarah Nolan',
                'password'          => Hash::make('Password123!'),
                'role'              => 'carer',
                'email_verified_at' => now(),
                'status'            => 'approved',
                'approved_at'       => now(),
            ]
        );

        $carer2 = User::updateOrCreate(
            ['email' => 'carer2@test.com'],
            [
                'name'              => 'Emma Ryan',
                'password'          => Hash::make('Password123!'),
                'role'              => 'carer',
                'email_verified_at' => now(),
                'status'            => 'approved',
                'approved_at'       => now(),
            ]
        );

        $parent = User::updateOrCreate(
            ['email' => 'parent@test.com'],
            [
                'name'              => 'John Kelly',
                'password'          => Hash::make('Password123!'),
                'role'              => 'parent',
                'email_verified_at' => now(),
                'status'            => 'approved',
                'approved_at'       => now(),
            ]
        );

        $parent2 = User::updateOrCreate(
            ['email' => 'parent2@test.com'],
            [
                'name'              => "Lisa O'Brien",
                'password'          => Hash::make('Password123!'),
                'role'              => 'parent',
                'email_verified_at' => now(),
                'status'            => 'approved',
                'approved_at'       => now(),
            ]
        );

        $parent3 = User::updateOrCreate(
            ['email' => 'parent3@test.com'],
            [
                'name'              => 'Aoife Murphy',
                'password'          => Hash::make('Password123!'),
                'role'              => 'parent',
                'email_verified_at' => now(),
                'status'            => 'approved',
                'approved_at'       => now(),
            ]
        );

        $parent4 = User::where('email', 'parent4@test.com')->first();

        // -----------------------------------------------------------------------
        // 2. Refresh room metadata
        // -----------------------------------------------------------------------
        $roomA = Room::where('name', 'Bumblebees')->firstOrFail();
        $roomB = Room::where('name', 'Ladybirds')->firstOrFail();
        $roomC = Room::where('name', 'Caterpillars')->first();

        $roomA->forceFill([
            'age_band'    => '6-24 months',
            'capacity'    => 12,
            'description' => 'Toddler room with a focus on creative play and early language development.',
        ])->save();

        $roomB->forceFill([
            'age_band'    => '2-5 years',
            'capacity'    => 15,
            'description' => 'Pre-school room preparing children for primary school with structured group activities.',
        ])->save();

        if ($roomC) {
            $roomC->forceFill([
                'age_band'    => $roomC->age_band ?? '0-2 years',
                'capacity'    => $roomC->capacity ?? 10,
                'description' => $roomC->description ?? 'Baby and young toddler room with low staff-to-child ratio.',
            ])->save();
        }

        // -----------------------------------------------------------------------
        // 3. Carer ↔ room assignments
        //    Sarah Nolan → Bumblebees, Emma Ryan → Ladybirds
        // -----------------------------------------------------------------------
        DB::table('room_user')->updateOrInsert(
            ['room_id' => $roomA->id, 'user_id' => $carer->id],
            ['room_id' => $roomA->id, 'user_id' => $carer->id, 'start_date' => now()->toDateString()]
        );
        DB::table('room_user')->updateOrInsert(
            ['room_id' => $roomB->id, 'user_id' => $carer2->id],
            ['room_id' => $roomB->id, 'user_id' => $carer2->id, 'start_date' => now()->toDateString()]
        );

        // -----------------------------------------------------------------------
        // 4. Children — load all that have a room assigned by DatabaseSeeder
        // -----------------------------------------------------------------------
        $allChildren = Child::whereNotNull('room_id')->get();

        // Pick out a few we'll reference by name later
        $miaKelly    = Child::where('first_name', 'Mia')->where('last_name', 'Kelly')->first();
        $noahByrne   = Child::where('first_name', 'Noah')->where('last_name', 'Byrne')->first();
        $lilyMurphy  = Child::where('first_name', 'Lily')->where('last_name', 'Murphy')->first();
        $oliverDavis = Child::where('first_name', 'Oliver')->where('last_name', 'Davis')->first();
        $avaOBrien   = Child::where('first_name', 'Ava')->where('last_name', "O'Brien")->first();
        $charlie     = Child::where('first_name', 'Charlie')->where('last_name', 'White')->first();
        $ellaDoyle   = Child::where('first_name', 'Ella')->where('last_name', 'Doyle')->first();
        $finnNolan   = Child::where('first_name', 'Finn')->where('last_name', 'Nolan')->first();
        $jackWalsh   = Child::where('first_name', 'Jack')->where('last_name', 'Walsh')->first();

        // -----------------------------------------------------------------------
        // 5. Build the last 14 weekdays (index 0 = today … index 13 = oldest)
        // -----------------------------------------------------------------------
        $weekdays = collect();
        $cursor   = Carbon::today()->copy();
        while ($weekdays->count() < 14) {
            if (! $cursor->isWeekend()) {
                $weekdays->push($cursor->copy());
            }
            $cursor->subDay();
        }

        // Map each child to the carer who covers its room
        $carerForRoom = function (Child $child) use ($roomA, $carer, $carer2) {
            return $child->room_id === $roomA->id ? $carer : $carer2;
        };

        // -----------------------------------------------------------------------
        // 6. Attendance — all 14 weekdays except today (DatabaseSeeder owns today)
        // -----------------------------------------------------------------------
        foreach ($allChildren as $child) {
            $childCarer = $carerForRoom($child);

            foreach ($weekdays->slice(1) as $day) {
                $absent = (random_int(1, 10) === 1); // ~10% absent

                Attendance::updateOrCreate(
                    ['child_id' => $child->id, 'date' => $day->toDateString()],
                    [
                        'status'       => $absent ? 'absent' : 'present',
                        'room_id'      => $child->room_id,
                        'recorded_by'  => $childCarer->id,
                        'check_in_at'  => $absent ? null : $day->copy()->setTime(8, random_int(30, 59)),
                        'check_out_at' => $absent ? null : $day->copy()->setTime(17, random_int(0, 30)),
                    ]
                );
            }
        }

        // -----------------------------------------------------------------------
        // 7. Daily updates — past 9 weekdays (today already done by DatabaseSeeder)
        // -----------------------------------------------------------------------
        $mealOptions = [
            'Porridge and fruit for breakfast. Chicken and vegetables for lunch. Yoghurt for snack.',
            'Toast with butter for breakfast. Pasta with tomato sauce for lunch. Crackers and cheese for snack.',
            'Cereal and banana for breakfast. Fish fingers and chips for lunch. Apple slices for snack.',
            'Scrambled eggs on toast for breakfast. Beef stew with rice for lunch. Raisins and cheese for snack.',
            'Pancakes for breakfast. Vegetable soup with bread for lunch. Cucumber and hummus for snack.',
        ];
        $sleepOptions = [
            'Morning nap 10:00–10:45. Afternoon nap 1:00–2:30.',
            'Napped 12:30–2:00. Settled quickly.',
            'Short nap 11:00–11:30. Did not settle for afternoon nap.',
            'Long nap 1:00–2:45. Woke in great form.',
            'Napped 10:30–11:15. Active all afternoon.',
        ];
        $noteOptions = [
            'Great day! Very chatty and playful.',
            'A little clingy at drop-off but settled within 10 minutes.',
            'Loved playing outside in the sandpit today.',
            'Painted a lovely picture for mammy and daddy.',
            'Tried new foods at lunch — loved the carrots!',
            'Had a wonderful time at music and movement.',
        ];

        foreach ($allChildren as $child) {
            $childCarer = $carerForRoom($child);

            foreach ($weekdays->slice(1, 9)->values() as $idx => $day) {
                DailyUpdate::updateOrCreate(
                    ['child_id' => $child->id, 'date' => $day->toDateString()],
                    [
                        'meals'      => $mealOptions[$idx % count($mealOptions)],
                        'sleep'      => $sleepOptions[$idx % count($sleepOptions)],
                        'notes'      => $noteOptions[($child->id + $idx) % count($noteOptions)],
                        'created_by' => $childCarer->id,
                    ]
                );
            }
        }

        // -----------------------------------------------------------------------
        // 8. Daily reports — narrative reports spread across the 2 weeks
        // -----------------------------------------------------------------------
        $reportNarratives = [
            "had a wonderful day today. They were very engaged during our art session and created a lovely painting using autumn colours. At lunchtime they ate all their vegetables which was great to see.",
            "had a fantastic day. They were very curious at storytime and asked lots of great questions. They played well with the other children and tried all of their lunch.",
            "had a lovely settled day. They napped well and were very chatty after waking up. They particularly enjoyed our sensory play station this afternoon.",
            "had a good day overall. A little unsettled after drop-off but cheered up quickly once involved in building blocks. Ate most of lunch and had a long restful nap.",
            "had a brilliant day! First one up for dancing at circle time and had the whole room laughing. Ate very well and helped tidy up toys without being asked.",
            "had a great morning at music and movement — really got involved with the shakers and bells. Settled well for nap and woke up smiling.",
            "spent most of the morning at the play kitchen serving us imaginary cups of tea. Enjoyed our outdoor walk and looked at the leaves changing colour.",
            "had a calm and happy day. Particularly enjoyed our story about the very hungry caterpillar — asked us to read it twice!",
            "was full of energy today. Loved running around at outdoor play and was very proud of building a tall tower at construction time.",
            "had a great day with friends. Shared toys nicely all morning and showed lovely manners at lunchtime. Lovely to see them growing in confidence.",
        ];

        $reportTargets = collect([$miaKelly, $noahByrne, $lilyMurphy, $avaOBrien, $charlie])->filter()->values();

        $reportSlot = 0;
        foreach ($reportTargets as $i => $child) {
            // 2 reports each spread across the 2 weeks: a recent one and an older one
            $offsets = [$i, 5 + $i];
            foreach ($offsets as $offset) {
                if (! isset($weekdays[$offset])) continue;
                $day = $weekdays[$offset];
                $childCarer = $carerForRoom($child);

                DailyReport::updateOrCreate(
                    ['child_id' => $child->id, 'date' => $day->toDateString()],
                    [
                        'carer_id'     => $childCarer->id,
                        'daily_report' => $child->first_name . ' ' . $reportNarratives[$reportSlot % count($reportNarratives)],
                    ]
                );
                $reportSlot++;
            }
        }

        // -----------------------------------------------------------------------
        // 9. Medication logs — 5 records spread across the last 2 weeks
        // -----------------------------------------------------------------------
        $medications = [
            // [child, name, dosage, time, notes, daysAgo]
            [$miaKelly,    'Calpol',         '5ml',     '10:30:00', 'Temperature of 37.8°C, parent notified.',                       1],
            [$avaOBrien,   'Antihistamine',  '2.5ml',   '14:00:00', 'Mild rash after lunch — administered as per care plan.',        3],
            [$charlie,     'Inhaler',        '2 puffs', '11:00:00', 'Routine asthma management before outdoor play.',                 6],
            [$noahByrne,   'Calpol',         '5ml',     '13:15:00', 'Slight temperature, parent contacted by phone.',                 9],
            [$charlie,     'Inhaler',        '2 puffs', '10:45:00', 'Routine asthma management before outdoor play.',                12],
        ];

        foreach ($medications as [$child, $name, $dosage, $time, $notes, $daysAgo]) {
            if (! $child) continue;
            $date = Carbon::today()->subDays($daysAgo);
            // Skip weekends
            while ($date->isWeekend()) {
                $date->subDay();
            }
            $childCarer = $carerForRoom($child);

            $exists = MedicationLog::where('child_id', $child->id)
                ->where('date', $date->toDateString())
                ->where('medication_name', $name)
                ->exists();

            if (! $exists) {
                MedicationLog::create([
                    'child_id'        => $child->id,
                    'carer_id'        => $childCarer->id,
                    'medication_name' => $name,
                    'dosage'          => $dosage,
                    'date'            => $date->toDateString(),
                    'time_given'      => $time,
                    'notes'           => $notes,
                ]);
            }
        }

        // -----------------------------------------------------------------------
        // 10. Incident reports — 6 across 2 weeks
        //     2 still "open" with dates 12+ days ago so they show as overdue
        // -----------------------------------------------------------------------
        $incidents = [
            // [child, title, description, action, severity, time, daysAgo, status]
            [$miaKelly,  'Bumped head on table edge',  'Child stood up quickly and bumped forehead on the corner of the activity table. Child cried briefly then settled.', 'Applied cold compress, child recovered quickly. Parent notified by phone.',                              'medium', '12:30:00', 1,  'closed'],
            [$noahByrne, 'Fall during outdoor play',   'Child tripped on the decking edge during outdoor play and grazed the right knee. No other injuries noted.',           'Minor graze on knee cleaned with antiseptic wipe and plaster applied. Child returned to play.',         'low',    '10:15:00', 4,  'closed'],
            [$avaOBrien, 'Possible allergic reaction', 'Mild rash appeared around mouth and neck approximately 20 minutes after afternoon snack.',                            'Antihistamine administered as per care plan. Parent contacted immediately. Rash cleared within an hour.', 'high',   '14:10:00', 7,  'closed'],
            [$charlie,   'Bit by another child',       'Child was bitten on the left arm by another child during free play. No skin broken.',                                 'Area cleaned and observed. Incident discussed sensitively with both families. Both children comforted.',  'medium', '11:45:00', 9,  'closed'],
            // Two long-standing open ones (overdue dashboard alerts)
            [$ellaDoyle,  'Slipped on wet floor',       'Child slipped on a wet patch of floor near the bathroom and landed on their bottom. No visible injury.',             'Reminded all children to walk indoors. Floor signage reviewed by manager.',                              'low',    '09:50:00', 13, 'open'],
            [$finnNolan,  'Minor bump at snack time',   'Child knocked elbow against the snack table while reaching for a cup. Slight redness, no swelling.',                  'Comforted child, applied a cool cloth, monitored for 15 minutes.',                                       'low',    '15:05:00', 12, 'open'],
        ];

        foreach ($incidents as [$child, $title, $desc, $action, $severity, $time, $daysAgo, $status]) {
            if (! $child) continue;
            $incDate = Carbon::today()->subDays($daysAgo);
            while ($incDate->isWeekend()) {
                $incDate->subDay();
            }
            $childCarer = $carerForRoom($child);

            $exists = IncidentReport::where('child_id', $child->id)
                ->where('incident_date', $incDate->toDateString())
                ->where('title', $title)
                ->exists();

            if (! $exists) {
                IncidentReport::create([
                    'child_id'                => $child->id,
                    'carer_id'                => $childCarer->id,
                    'room_id'                 => $child->room_id,
                    'incident_date'           => $incDate->toDateString(),
                    'incident_time'           => $time,
                    'title'                   => $title,
                    'description'             => $desc,
                    'action_taken'            => $action,
                    'severity'                => $severity,
                    'parent_contact_required' => true,
                    'status'                  => $status,
                ]);
            }
        }

        // -----------------------------------------------------------------------
        // 11. Invoices — March + April for 3 parents (6 total)
        // -----------------------------------------------------------------------
        if ($miaKelly) {
            $this->makeInvoice($miaKelly, $parent, '2026-03-01', '2026-03-31', '2026-03-31', 850, 0, 'paid', [
                ['Full-day care Mon–Fri', 1, 800.00],
                ['Activity fee',          1,  50.00],
            ], [
                'payment_status'      => 'approved',
                'payment_approved_at' => now()->subDays(20),
                'payment_approved_by' => $manager->id,
            ]);
            $this->makeInvoice($miaKelly, $parent, '2026-04-01', '2026-04-30', '2026-04-30', 850, 0, 'sent', [
                ['Full-day care Mon–Fri', 1, 800.00],
                ['Activity fee',          1,  50.00],
            ], [
                'payment_status' => 'unpaid',
            ]);
        }

        if ($lilyMurphy) {
            $this->makeInvoice($lilyMurphy, $parent2, '2026-03-01', '2026-03-31', '2026-03-31', 650, 0, 'paid', [
                ['3-day care Mon/Wed/Fri', 1, 600.00],
                ['Activity fee',           1,  50.00],
            ], [
                'payment_status'      => 'approved',
                'payment_approved_at' => now()->subDays(18),
                'payment_approved_by' => $manager->id,
            ]);
            $this->makeInvoice($lilyMurphy, $parent2, '2026-04-01', '2026-04-30', '2026-04-30', 650, 0, 'sent', [
                ['3-day care Mon/Wed/Fri', 1, 600.00],
                ['Activity fee',           1,  50.00],
            ], [
                'payment_status'       => 'payment_submitted',
                'payment_submitted_at' => now()->subDays(3),
                'payment_notes'        => 'Bank transfer receipt uploaded.',
            ]);
        }

        if ($oliverDavis) {
            $this->makeInvoice($oliverDavis, $parent3, '2026-03-01', '2026-03-31', '2026-03-31', 900, 0, 'paid', [
                ['Full-day care Mon–Fri', 1, 850.00],
                ['Activity fee',          1,  50.00],
            ], [
                'payment_status'      => 'approved',
                'payment_approved_at' => now()->subDays(15),
                'payment_approved_by' => $manager->id,
            ]);
            $this->makeInvoice($oliverDavis, $parent3, '2026-04-01', '2026-04-30', '2026-04-30', 900, 0, 'draft', [
                ['Full-day care Mon–Fri', 1, 850.00],
                ['Activity fee',          1,  50.00],
            ]);
        }

        // -----------------------------------------------------------------------
        // 12. Acknowledgements — 2 acknowledged + 2 pending
        // -----------------------------------------------------------------------

        // Pending — today's report for Mia Kelly (parent: John Kelly)
        $pendingReport = DailyReport::where('child_id', $miaKelly?->id)
            ->where('date', Carbon::today()->toDateString())
            ->first();
        if ($pendingReport) {
            Acknowledgement::updateOrCreate(
                ['record_type' => 'daily_report', 'record_id' => $pendingReport->id, 'parent_id' => $parent->id],
                ['status' => 'pending', 'signed_at' => null, 'signature_name' => null]
            );
        }

        // Acknowledged — an older report for Mia Kelly
        $signedReport = DailyReport::where('child_id', $miaKelly?->id)
            ->orderByDesc('date')
            ->skip(1)->first();
        if ($signedReport) {
            Acknowledgement::updateOrCreate(
                ['record_type' => 'daily_report', 'record_id' => $signedReport->id, 'parent_id' => $parent->id],
                ['status' => 'acknowledged', 'signed_at' => now()->subDays(2), 'signature_name' => $parent->name]
            );
        }

        // Pending — a high-severity incident
        $highIncident = IncidentReport::where('severity', 'high')->first();
        if ($highIncident) {
            $incParent = DB::table('child_parent')
                ->where('child_id', $highIncident->child_id)
                ->first();
            if ($incParent) {
                Acknowledgement::updateOrCreate(
                    ['record_type' => 'incident_report', 'record_id' => $highIncident->id, 'parent_id' => $incParent->parent_id],
                    ['status' => 'pending', 'signed_at' => null, 'signature_name' => null]
                );
            }
        }

        // Acknowledged — a low-severity incident
        $lowIncident = IncidentReport::where('severity', 'low')
            ->where('status', 'closed')
            ->first();
        if ($lowIncident) {
            $incParent = DB::table('child_parent')
                ->where('child_id', $lowIncident->child_id)
                ->first();
            if ($incParent) {
                $parentUser = User::find($incParent->parent_id);
                Acknowledgement::updateOrCreate(
                    ['record_type' => 'incident_report', 'record_id' => $lowIncident->id, 'parent_id' => $incParent->parent_id],
                    ['status' => 'acknowledged', 'signed_at' => now()->subDay(), 'signature_name' => $parentUser?->name ?? 'Parent']
                );
            }
        }

        // -----------------------------------------------------------------------
        // 13. Milestone observations — 5 children, ~50% of age-appropriate milestones
        // -----------------------------------------------------------------------
        $allMilestones = Milestone::all();

        if ($allMilestones->isNotEmpty()) {
            $observationNotes = [
                'Observed during free play',
                'Demonstrated at lunch today',
                'Showed this during outdoor activity',
                'Parents confirmed this at home too',
                'Observed consistently over the past week',
                'First observed during music and movement session',
            ];

            $observers = [$carer, $carer2];

            foreach ($allChildren->take(6) as $idx => $child) {
                $ageMonths = Carbon::parse($child->dob)->diffInMonths(now());

                $relevant = $allMilestones->filter(function ($m) use ($ageMonths) {
                    [$min, $max] = explode('-', $m->age_range_months);
                    return $ageMonths >= (int) $min && $ageMonths <= ((int) $max + 6);
                });

                if ($relevant->isEmpty()) {
                    continue;
                }

                $count     = max(1, (int) ($relevant->count() * 0.5));
                $toObserve = $relevant->random(min($count, $relevant->count()));

                foreach ($toObserve as $milestone) {
                    DB::table('child_milestones')->updateOrInsert(
                        ['child_id' => $child->id, 'milestone_id' => $milestone->id],
                        [
                            'child_id'     => $child->id,
                            'milestone_id' => $milestone->id,
                            'observed_by'  => $observers[$idx % count($observers)]->id,
                            'observed_at'  => Carbon::today()->subDays(random_int(1, 60))->toDateString(),
                            'notes'        => $observationNotes[array_rand($observationNotes)],
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]
                    );
                }
            }
        }

        // -----------------------------------------------------------------------
        // 14. Messages — 3 conversation threads
        // -----------------------------------------------------------------------

        // Thread 1: Sarah Nolan (carer) ↔ John Kelly (parent) — about Mia Kelly
        $this->seedThread(
            $carer, $parent, $miaKelly,
            [
                [$carer,  $parent,  "Hi! Just wanted to let you know {$miaKelly?->first_name} had a great day today. She's really settling in well."],
                [$parent, $carer,   "That's lovely to hear, thank you! She talks about crèche all the time at home."],
                [$carer,  $parent,  "She made a beautiful painting today, I'll put it in her bag. Also a reminder — we have pyjama day on Friday!"],
                [$parent, $carer,   "Oh brilliant, she'll love that! Thanks for letting me know."],
            ]
        );

        // Thread 2: Emma Ryan (carer2) ↔ Lisa O'Brien (parent2) — about Ava O'Brien
        $this->seedThread(
            $carer2, $parent2, $avaOBrien,
            [
                [$parent2, $carer2,  "Hi, {$avaOBrien?->first_name} has been a bit under the weather. Can you keep an eye on her today?"],
                [$carer2,  $parent2, "Of course! I'll monitor her and let you know how she gets on."],
                [$carer2,  $parent2, "Update: {$avaOBrien?->first_name} perked up after lunch and played happily this afternoon. No temperature."],
            ]
        );

        // Thread 3: Aoife Murphy (parent3) ↔ Mary Collins (manager)
        $this->seedThread(
            $parent3, $manager, $oliverDavis,
            [
                [$parent3, $manager,  "Hi, I'd like to discuss changing from 3 days to 5 days per week. Is there availability?"],
                [$manager, $parent3,  "Hi Aoife, thanks for reaching out. We do have availability in Ladybirds — I'll send you an updated invoice this week."],
            ]
        );

        // -----------------------------------------------------------------------
        // 15. Live demo data — fresh entries for yesterday + today
        //     so dashboards feel "live" the moment a demo opens them.
        //     Idempotent: re-running this seeder only refreshes the rows.
        // -----------------------------------------------------------------------

        // Resolve to the most recent weekdays (skip Sat/Sun)
        $todayLive = Carbon::today()->copy();
        while ($todayLive->isWeekend()) {
            $todayLive->subDay();
        }
        $yesterdayLive = $todayLive->copy()->subDay();
        while ($yesterdayLive->isWeekend()) {
            $yesterdayLive->subDay();
        }
        $liveDays = [$yesterdayLive, $todayLive];

        $liveMeals = [
            'Porridge with banana for breakfast. Roast chicken, mash and peas for lunch. Yoghurt and fruit for snack.',
            'Toast and scrambled egg for breakfast. Spaghetti bolognese for lunch. Cheese and crackers for snack.',
            'Weetabix and milk for breakfast. Shepherd\'s pie for lunch. Apple slices and rice cakes for snack.',
            'Pancakes with berries for breakfast. Salmon, potatoes and broccoli for lunch. Carrot sticks and hummus for snack.',
            'Fruit and yoghurt for breakfast. Mild chicken curry with rice for lunch. Banana bread for snack.',
            'Wholemeal toast with cream cheese for breakfast. Lasagne and salad for lunch. Cucumber and pita for snack.',
        ];
        $liveSleep = [
            'Settled quickly for nap 12:30–2:00, woke up smiling.',
            'Long nap 12:45–2:30 — very well rested.',
            'Short rest 1:00–1:30, full of energy in the afternoon.',
            'Napped 1:00–2:15 with their favourite teddy.',
            'Cosy nap from 12:30–2:00 after lunch.',
            'Quick power nap 1:15–1:50, back to play afterwards.',
        ];
        $liveNotes = [
            'Loved music time today, danced through every song.',
            'Asked us to read the hungry caterpillar twice — a clear favourite.',
            'Chose the painting station first thing — very focused work.',
            'Tried new foods at lunch and gave a thumbs up to the broccoli!',
            'Gentle and kind to a friend who was upset, lovely to see.',
            'Spent ages at the water tray — splashing and pouring happily.',
            'Built the tallest tower yet at construction play, very proud.',
            'Helped tidy up without being asked — beaming the whole time.',
        ];

        // ---- 1 & 2. Attendance + Daily Updates for every child, both days ----
        foreach ($liveDays as $dayIndex => $day) {
            foreach ($allChildren as $child) {
                $childCarer = $carerForRoom($child);
                $absent = (random_int(1, 10) === 1); // ~10% absent

                Attendance::updateOrCreate(
                    ['child_id' => $child->id, 'date' => $day->toDateString()],
                    [
                        'status'       => $absent ? 'absent' : 'present',
                        'room_id'      => $child->room_id,
                        'recorded_by'  => $childCarer->id,
                        'check_in_at'  => $absent ? null : $day->copy()->setTime(8, random_int(30, 59)),
                        'check_out_at' => $absent ? null : $day->copy()->setTime(17, random_int(0, 30)),
                    ]
                );

                if (! $absent) {
                    DailyUpdate::updateOrCreate(
                        ['child_id' => $child->id, 'date' => $day->toDateString()],
                        [
                            'meals'      => $liveMeals[($child->id + $dayIndex) % count($liveMeals)],
                            'sleep'      => $liveSleep[($child->id + $dayIndex + 1) % count($liveSleep)],
                            'notes'      => $liveNotes[($child->id + $dayIndex + 2) % count($liveNotes)],
                            'created_by' => $childCarer->id,
                        ]
                    );
                }
            }
        }

        // ---- 3. Daily reports — narrative for 4-5 children, both days ----
        $liveReportNarratives = [
            "had a wonderful day today. {pronoun_he_she} was very engaged during our morning art session and created a beautiful painting using autumn leaves. At lunchtime {pronoun_he_she} ate everything on {pronoun_his_her} plate, including the broccoli! {pronoun_he_she} had a good nap from 12:30 to 2pm and woke up in great spirits. {pronoun_he_she} enjoyed our afternoon storytime, particularly the book about the hungry caterpillar. Looking forward to seeing {pronoun_him_her} tomorrow.",
            "had a brilliant morning. {pronoun_he_she} was the first to choose music time and led the room in singing the wheels on the bus. At snack {pronoun_he_she} tried new fruit for the first time and asked for seconds. After a long restful nap from 12:45 to 2:15, {pronoun_he_she} settled into the home corner with friends, sharing toys beautifully. A really lovely day all round.",
            "had a calm and contented day. {pronoun_he_she} spent a happy stretch at the sensory tray exploring rice and small pots — focused for almost twenty minutes. {pronoun_he_she} ate a great lunch and napped well. In the afternoon {pronoun_he_she} loved our outdoor walk and pointed at every leaf {pronoun_he_she} could see.",
            "was full of beans today! {pronoun_he_she} ran straight to outdoor play and spent the morning in the sandpit making 'cakes' for everyone. Lunch went down a treat, especially the pasta. {pronoun_he_she} had a short nap and bounced back ready for craft time, where {pronoun_he_she} made a lovely card to take home.",
            "had a really sociable day. {pronoun_he_she} played beautifully with friends during free play and was so kind when one of the younger children was upset — helped settle them with a teddy. {pronoun_he_she} ate well, napped well, and finished the day proudly showing off the tower {pronoun_he_she} built at construction time.",
        ];

        $liveReportTargets = collect([
            $miaKelly,    // Bumblebees
            $noahByrne,   // Bumblebees
            $avaOBrien,   // Ladybirds
            $charlie,     // Ladybirds
            $lilyMurphy,  // Bumblebees
        ])->filter()->values();

        $liveSlot = 0;
        foreach ($liveReportTargets as $child) {
            foreach ($liveDays as $day) {
                $childCarer = $carerForRoom($child);
                $narrative = $liveReportNarratives[$liveSlot % count($liveReportNarratives)];
                // Lightweight pronoun substitution — keeps text personal without per-child branches
                $body = strtr($narrative, [
                    '{pronoun_he_she}'  => 'they',
                    '{pronoun_his_her}' => 'their',
                    '{pronoun_him_her}' => 'them',
                ]);

                DailyReport::updateOrCreate(
                    ['child_id' => $child->id, 'date' => $day->toDateString()],
                    [
                        'carer_id'     => $childCarer->id,
                        'daily_report' => $child->first_name . ' ' . $body,
                    ]
                );
                $liveSlot++;
            }
        }

        // ---- 4. Medication log — 1-2 entries for today ----
        $liveMeds = [
            // Charlie White has asthma per his medical_notes — routine inhaler before outdoor play
            [$charlie,   'Inhaler', '2 puffs', '10:30:00', 'Routine asthma management before outdoor play.'],
            // Noah feeling a bit warm today — Calpol after parent consent
            [$noahByrne, 'Calpol',  '5ml',     '13:00:00', 'Mild temperature of 37.6°C, parent consented by phone.'],
        ];

        foreach ($liveMeds as [$child, $name, $dosage, $time, $notes]) {
            if (! $child) continue;
            $childCarer = $carerForRoom($child);

            $exists = MedicationLog::where('child_id', $child->id)
                ->where('date', $todayLive->toDateString())
                ->where('medication_name', $name)
                ->exists();

            if (! $exists) {
                MedicationLog::create([
                    'child_id'        => $child->id,
                    'carer_id'        => $childCarer->id,
                    'medication_name' => $name,
                    'dosage'          => $dosage,
                    'date'            => $todayLive->toDateString(),
                    'time_given'      => $time,
                    'notes'           => $notes,
                ]);
            }
        }

        // ---- 5. Incident report — 1 minor for yesterday (open, low) ----
        $incidentTitle = 'Minor scrape on knee during outdoor play';
        $incidentChild = $jackWalsh ?: ($oliverDavis ?: $allChildren->first());

        if ($incidentChild) {
            $exists = IncidentReport::where('child_id', $incidentChild->id)
                ->where('incident_date', $yesterdayLive->toDateString())
                ->where('title', $incidentTitle)
                ->exists();

            if (! $exists) {
                IncidentReport::create([
                    'child_id'                => $incidentChild->id,
                    'carer_id'                => $carerForRoom($incidentChild)->id,
                    'room_id'                 => $incidentChild->room_id,
                    'incident_date'           => $yesterdayLive->toDateString(),
                    'incident_time'           => '11:25:00',
                    'title'                   => $incidentTitle,
                    'description'             => 'Child slipped on the decking edge during outdoor play and grazed the right knee. No other injuries noted.',
                    'action_taken'            => 'Cleaned and plaster applied. Parent notified at pickup.',
                    'severity'                => 'low',
                    'parent_contact_required' => true,
                    'status'                  => 'open',
                ]);
            }
        }

        // ---- 6. Messages — 2-3 fresh today, all unread ----
        $liveMessageData = [
            [$parent,  $carer,   $miaKelly,   "Hi Sarah, just to let you know Mia might be a bit tired today, didn't sleep well last night.", 4],
            [$carer,   $parent,  $miaKelly,   "Thanks for letting us know! We'll keep an eye on her and make sure she has a good nap.", 3],
            [$parent2, $carer2,  $avaOBrien,  "Morning Emma — Ava forgot her hat at home, can she still join outdoor play?", 2],
        ];

        foreach ($liveMessageData as [$sender, $receiver, $child, $body, $hoursAgo]) {
            if (! $sender || ! $receiver) continue;

            $exists = Message::where('sender_id', $sender->id)
                ->where('receiver_id', $receiver->id)
                ->where('body', $body)
                ->exists();

            if (! $exists) {
                Message::create([
                    'sender_id'   => $sender->id,
                    'receiver_id' => $receiver->id,
                    'child_id'    => $child?->id,
                    'body'        => $body,
                    'read_at'     => null,
                    'created_at'  => now()->subHours($hoursAgo),
                    'updated_at'  => now()->subHours($hoursAgo),
                ]);
            }
        }

        // ---- 7. Milestone observations — 2-3 dated today/yesterday ----
        if ($allMilestones->isNotEmpty()) {
            $milestoneTargets = collect([$miaKelly, $noahByrne, $avaOBrien])->filter()->values();
            $observers        = [$carer, $carer2];

            foreach ($milestoneTargets as $idx => $child) {
                $ageMonths = Carbon::parse($child->dob)->diffInMonths(now());

                $relevant = $allMilestones->filter(function ($m) use ($ageMonths) {
                    [$min, $max] = explode('-', $m->age_range_months);
                    return $ageMonths >= (int) $min && $ageMonths <= ((int) $max + 6);
                });

                if ($relevant->isEmpty()) continue;

                // Prefer a milestone this child has not been observed against yet
                $alreadyObserved = DB::table('child_milestones')
                    ->where('child_id', $child->id)
                    ->pluck('milestone_id')
                    ->all();
                $available = $relevant->whereNotIn('id', $alreadyObserved);
                $milestone = $available->isNotEmpty() ? $available->random() : $relevant->random();

                $day = ($idx % 2 === 0) ? $todayLive : $yesterdayLive;

                DB::table('child_milestones')->updateOrInsert(
                    ['child_id' => $child->id, 'milestone_id' => $milestone->id],
                    [
                        'child_id'     => $child->id,
                        'milestone_id' => $milestone->id,
                        'observed_by'  => $observers[$idx % count($observers)]->id,
                        'observed_at'  => $day->toDateString(),
                        'notes'        => 'Observed clearly and consistently during free play.',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]
                );
            }
        }

        // ---- 8. Payment submission — one invoice with very recent timestamp ----
        // Refresh Lily Murphy's April invoice (already payment_submitted) so the
        // manager dashboard's "Pending Payments" card shows live activity.
        $pendingPaymentInvoice = null;
        if ($lilyMurphy) {
            $pendingPaymentInvoice = Invoice::where('child_id', $lilyMurphy->id)
                ->where('period_start', '2026-04-01')
                ->first();
        }
        if (! $pendingPaymentInvoice) {
            $pendingPaymentInvoice = Invoice::where('payment_status', 'payment_submitted')->first();
        }

        if ($pendingPaymentInvoice) {
            $pendingPaymentInvoice->update([
                'payment_status'       => 'payment_submitted',
                'payment_submitted_at' => now()->subHours(2),
                'payment_proof_path'   => 'payment_proofs/demo-receipt.png',
                'payment_notes'        => $pendingPaymentInvoice->payment_notes ?: 'Bank transfer receipt uploaded.',
                'rejection_reason'     => null,
            ]);
        }

        // -----------------------------------------------------------------------
        // 16. Staff qualifications + live clock-ins (Tusla compliance demo data)
        // -----------------------------------------------------------------------
        $qualSeed = [
            'carer@test.com' => [
                ['type' => 'education',        'name' => 'QQI Level 6 in Early Childhood Care and Education', 'issuer' => 'QQI',                       'issued_date' => '2020-06-15',                                            'expires_at' => null],
                ['type' => 'garda_vetting',    'name' => 'Garda Vetting Disclosure',                          'issuer' => 'National Vetting Bureau',   'issued_date' => '2024-01-10',                                            'expires_at' => now()->addMonths(6)->toDateString()],
                ['type' => 'first_aid',        'name' => 'Paediatric First Aid',                              'issuer' => 'Irish Heart Foundation',    'issued_date' => '2024-09-12',                                            'expires_at' => now()->addDays(45)->toDateString()],
                ['type' => 'child_protection', 'name' => 'Children First e-Learning',                         'issuer' => 'Tusla',                     'issued_date' => '2025-02-20',                                            'expires_at' => now()->addYears(2)->toDateString()],
            ],
            'carer2@test.com' => [
                ['type' => 'education',     'name' => 'QQI Level 5 in Early Childhood Care and Education', 'issuer' => 'QQI',                     'issued_date' => '2022-06-15',                                            'expires_at' => null],
                ['type' => 'garda_vetting', 'name' => 'Garda Vetting Disclosure',                          'issuer' => 'National Vetting Bureau', 'issued_date' => '2023-11-15',                                            'expires_at' => now()->addDays(20)->toDateString()],
                ['type' => 'first_aid',     'name' => 'Paediatric First Aid',                              'issuer' => 'Order of Malta',          'issued_date' => '2025-03-10',                                            'expires_at' => now()->addYears(2)->toDateString()],
                ['type' => 'food_safety',   'name' => 'HACCP Level 2',                                     'issuer' => 'NSAI',                    'issued_date' => '2024-10-05',                                            'expires_at' => now()->addMonths(3)->toDateString()],
            ],
        ];

        foreach ($qualSeed as $email => $quals) {
            $carerUser = User::where('email', $email)->first();
            if (! $carerUser) {
                continue;
            }
            foreach ($quals as $q) {
                \App\Models\StaffQualification::firstOrCreate(
                    ['user_id' => $carerUser->id, 'type' => $q['type'], 'name' => $q['name']],
                    $q
                );
            }
        }

        foreach (['carer@test.com', 'carer2@test.com'] as $email) {
            $carerUser = User::where('email', $email)->first();
            if (! $carerUser) {
                continue;
            }
            if (! $carerUser->isClockedIn()) {
                \App\Models\StaffClockIn::create([
                    'user_id'        => $carerUser->id,
                    'clocked_in_at'  => now()->setTime(8, 30),
                    'clocked_out_at' => null,
                    'room_id'        => $carerUser->activeRooms()->first()?->id,
                ]);
            }
        }
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    private function makeInvoice(
        Child  $child,
        User   $parent,
        string $start,
        string $end,
        string $due,
        float  $total,
        float  $discount,
        string $status,
        array  $items,
        array  $extra = []
    ): Invoice {
        $invoice = Invoice::updateOrCreate(
            [
                'child_id'     => $child->id,
                'parent_id'    => $parent->id,
                'period_start' => $start,
                'period_end'   => $end,
            ],
            array_merge([
                'due_date'  => $due,
                'total'     => $total,
                'discount'  => $discount,
                'status'    => $status,
            ], $extra)
        );

        // Only add line items on first creation (avoid duplicates on re-run)
        if ($invoice->wasRecentlyCreated) {
            foreach ($items as [$desc, $qty, $unitPrice]) {
                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'description' => $desc,
                    'qty'         => $qty,
                    'unit_price'  => $unitPrice,
                    'total'       => $qty * $unitPrice,
                ]);
            }
        }

        return $invoice;
    }

    /**
     * Create a conversation thread only if no messages already exist between the two users.
     * Leaves the last 2 messages unread; marks earlier ones read.
     */
    private function seedThread(User $userA, User $userB, ?Child $child, array $messages): void
    {
        $exists = Message::where(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userA->id)->where('receiver_id', $userB->id);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userB->id)->where('receiver_id', $userA->id);
        })->exists();

        if ($exists) {
            return;
        }

        $total = count($messages);
        foreach ($messages as $i => [$sender, $receiver, $body]) {
            Message::create([
                'sender_id'   => $sender->id,
                'receiver_id' => $receiver->id,
                'child_id'    => $child?->id,
                'body'        => $body,
                // Mark all but the last 2 as read
                'read_at'     => ($i < $total - 2) ? now()->subHours(random_int(1, 12)) : null,
            ]);
        }
    }
}
