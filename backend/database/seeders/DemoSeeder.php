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
            ]
        );

        $carer = User::updateOrCreate(
            ['email' => 'carer@test.com'],
            [
                'name'              => 'Sarah Nolan',
                'password'          => Hash::make('Password123!'),
                'role'              => 'carer',
                'email_verified_at' => now(),
            ]
        );

        $carer2 = User::updateOrCreate(
            ['email' => 'carer2@test.com'],
            [
                'name'              => 'Emma Ryan',
                'password'          => Hash::make('Password123!'),
                'role'              => 'carer',
                'email_verified_at' => now(),
            ]
        );

        $parent = User::updateOrCreate(
            ['email' => 'parent@test.com'],
            [
                'name'              => 'John Kelly',
                'password'          => Hash::make('Password123!'),
                'role'              => 'parent',
                'email_verified_at' => now(),
            ]
        );

        $parent2 = User::updateOrCreate(
            ['email' => 'parent2@test.com'],
            [
                'name'              => "Lisa O'Brien",
                'password'          => Hash::make('Password123!'),
                'role'              => 'parent',
                'email_verified_at' => now(),
            ]
        );

        $parent3 = User::updateOrCreate(
            ['email' => 'parent3@test.com'],
            [
                'name'              => 'Aoife Murphy',
                'password'          => Hash::make('Password123!'),
                'role'              => 'parent',
                'email_verified_at' => now(),
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
