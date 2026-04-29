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
        // References to users / rooms already created by DatabaseSeeder
        // -----------------------------------------------------------------------
        $parent  = User::where('email', 'parent@test.com')->firstOrFail();
        $parent2 = User::where('email', 'parent2@test.com')->firstOrFail();
        $parent3 = User::where('email', 'parent3@test.com')->first();
        $parent4 = User::where('email', 'parent4@test.com')->first();
        $carer   = User::where('email', 'carer@test.com')->firstOrFail();

        $roomA = Room::where('name', 'Bumblebees')->firstOrFail();
        $roomB = Room::where('name', 'Ladybirds')->firstOrFail();
        $roomC = Room::where('name', 'Caterpillars')->first();

        // Backfill capacity / description on the seeded rooms
        $roomA->forceFill([
            'age_band'    => $roomA->age_band ?? '2-4 years',
            'capacity'    => $roomA->capacity ?? 15,
            'description' => $roomA->description ?? 'Toddler room with a focus on creative play and early language development.',
        ])->save();

        $roomB->forceFill([
            'age_band'    => $roomB->age_band ?? '3-5 years',
            'capacity'    => $roomB->capacity ?? 18,
            'description' => $roomB->description ?? 'Pre-school room preparing children for primary school with structured group activities.',
        ])->save();

        if ($roomC) {
            $roomC->forceFill([
                'age_band'    => $roomC->age_band ?? '0-2 years',
                'capacity'    => $roomC->capacity ?? 10,
                'description' => $roomC->description ?? 'Baby and young toddler room with low staff-to-child ratio.',
            ])->save();
        }

        // -----------------------------------------------------------------------
        // 1. Additional carer — Sarah Nolan, assigned to Bumblebees
        // -----------------------------------------------------------------------
        $sarahNolan = User::updateOrCreate(
            ['email' => 'sarah.nolan@snugbug.ie'],
            [
                'name'              => 'Sarah Nolan',
                'password'          => Hash::make('Password123!'),
                'role'              => 'carer',
                'email_verified_at' => now(),
            ]
        );
        DB::table('room_user')->updateOrInsert(
            ['room_id' => $roomA->id, 'user_id' => $sarahNolan->id],
            ['room_id' => $roomA->id, 'user_id' => $sarahNolan->id, 'start_date' => now()->toDateString()]
        );

        // -----------------------------------------------------------------------
        // 2. Load all children with a room assigned
        // -----------------------------------------------------------------------
        $allChildren = Child::whereNotNull('room_id')->get();

        // -----------------------------------------------------------------------
        // 3. Build last 10 weekdays (index 0 = today … index 9 = oldest)
        // -----------------------------------------------------------------------
        $weekdays = collect();
        $cursor   = Carbon::today()->copy();
        while ($weekdays->count() < 10) {
            if (! $cursor->isWeekend()) {
                $weekdays->push($cursor->copy());
            }
            $cursor->subDay();
        }

        // -----------------------------------------------------------------------
        // 4. Attendance — fill weekdays 1-9 (today = index 0, done by DatabaseSeeder)
        // -----------------------------------------------------------------------
        foreach ($allChildren as $child) {
            foreach ($weekdays->slice(1) as $day) {
                $absent = (random_int(1, 10) === 1); // ~10% absent

                Attendance::updateOrCreate(
                    ['child_id' => $child->id, 'date' => $day->toDateString()],
                    [
                        'status'       => $absent ? 'absent' : 'present',
                        'room_id'      => $child->room_id,
                        'recorded_by'  => $carer->id,
                        'check_in_at'  => $absent ? null : $day->copy()->setTime(8, random_int(30, 59)),
                        'check_out_at' => $absent ? null : $day->copy()->setTime(17, random_int(0, 30)),
                    ]
                );
            }
        }

        // -----------------------------------------------------------------------
        // 5. Daily updates — weekdays 1-4 with varied content
        //    (today = index 0, already handled by DatabaseSeeder)
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
            foreach ($weekdays->slice(1, 4)->values() as $idx => $day) {
                DailyUpdate::updateOrCreate(
                    ['child_id' => $child->id, 'date' => $day->toDateString()],
                    [
                        'meals'      => $mealOptions[$idx % count($mealOptions)],
                        'sleep'      => $sleepOptions[$idx % count($sleepOptions)],
                        'notes'      => $noteOptions[($child->id + $idx) % count($noteOptions)],
                        'created_by' => $carer->id,
                    ]
                );
            }
        }

        // -----------------------------------------------------------------------
        // 6. Daily reports — 5 children × last 3 days with narrative text
        // -----------------------------------------------------------------------
        $reportSuffixes = [
            'had a wonderful day today. She was very engaged during our art session and created a lovely painting using autumn colours. At lunchtime she ate all her vegetables which was great to see. During outdoor play she showed excellent sharing skills with her friends.',
            'had a fantastic day. He was very curious at storytime and asked lots of great questions. He played well with the other children and tried all of his lunch. His confidence in group activities has really grown this week.',
            'had a lovely settled day. She napped well and was very chatty after waking up. She particularly enjoyed our sensory play station this afternoon and was very focused throughout.',
            'had a good day overall. He was a little unsettled after drop-off but cheered up quickly once he got involved in building blocks. He ate most of his lunch and had a long restful nap.',
            'had a brilliant day! She was the first one up for dancing at circle time and had the whole room laughing. She ate very well and helped tidy up toys without being asked.',
        ];

        foreach ($allChildren->take(5)->values() as $i => $child) {
            foreach ($weekdays->take(3)->values() as $day) {
                DailyReport::updateOrCreate(
                    ['child_id' => $child->id, 'date' => $day->toDateString()],
                    [
                        'carer_id'     => $carer->id,
                        'daily_report' => $child->first_name . ' ' . $reportSuffixes[$i],
                    ]
                );
            }
        }

        // -----------------------------------------------------------------------
        // 7. Medication logs
        // -----------------------------------------------------------------------
        $miaKelly  = Child::where('first_name', 'Mia')->where('last_name', 'Kelly')->first();
        $avaOBrien = Child::where('first_name', 'Ava')->where('last_name', "O'Brien")->first();
        $charlie   = Child::where('first_name', 'Charlie')->where('last_name', 'White')->first();

        $today = Carbon::today()->toDateString();

        foreach ([
            [$miaKelly,  'Calpol',        '5ml',    '10:30:00', 'Temperature of 37.8°C, parent notified.'],
            [$avaOBrien, 'Antihistamine',  '2.5ml',  '14:00:00', 'Mild allergic reaction after lunch, applying care plan protocol.'],
            [$charlie,   'Inhaler',        '2 puffs','11:00:00', 'Routine asthma management before outdoor play.'],
        ] as [$child, $name, $dosage, $time, $notes]) {
            if (! $child) continue;
            $exists = MedicationLog::where('child_id', $child->id)
                ->where('date', $today)
                ->where('medication_name', $name)
                ->exists();
            if (! $exists) {
                MedicationLog::create([
                    'child_id'        => $child->id,
                    'carer_id'        => $carer->id,
                    'medication_name' => $name,
                    'dosage'          => $dosage,
                    'date'            => $today,
                    'time_given'      => $time,
                    'notes'           => $notes,
                ]);
            }
        }

        // -----------------------------------------------------------------------
        // 8. Incident reports
        // -----------------------------------------------------------------------
        $noahByrne = Child::where('first_name', 'Noah')->where('last_name', 'Byrne')->first();

        foreach ([
            [$miaKelly,  'Bumped head on table edge',  'Child stood up quickly and bumped forehead on the corner of the activity table. Child cried briefly then settled.', 'Applied cold compress, child recovered quickly. Parent notified by phone.',                             'medium', '12:30:00', 1],
            [$noahByrne, 'Fall during outdoor play',   'Child tripped on the decking edge during outdoor play and grazed the right knee. No other injuries noted.',           'Minor graze on knee cleaned with antiseptic wipe and plaster applied. Child returned to play.',       'low',    '10:15:00', 2],
            [$avaOBrien, 'Possible allergic reaction', 'Mild rash appeared around mouth and neck approximately 20 minutes after afternoon snack.',                            'Antihistamine administered as per care plan. Parent contacted immediately. Rash cleared within an hour.','high',   '14:10:00', 0],
            [$charlie,   'Bit by another child',       'Child was bitten on the left arm by another child during free play. No skin broken.',                                 'Area cleaned and observed. Incident discussed sensitively with both families. Both children comforted.',   'medium', '11:45:00', 3],
        ] as [$child, $title, $desc, $action, $severity, $time, $offset]) {
            if (! $child) continue;
            $incDate = Carbon::today()->subDays($offset)->toDateString();
            $exists  = IncidentReport::where('child_id', $child->id)
                ->where('incident_date', $incDate)
                ->where('title', $title)
                ->exists();
            if (! $exists) {
                IncidentReport::create([
                    'child_id'                => $child->id,
                    'carer_id'                => $carer->id,
                    'room_id'                 => $child->room_id,
                    'incident_date'           => $incDate,
                    'incident_time'           => $time,
                    'title'                   => $title,
                    'description'             => $desc,
                    'action_taken'            => $action,
                    'severity'                => $severity,
                    'parent_contact_required' => true,
                    'status'                  => 'open',
                ]);
            }
        }

        // -----------------------------------------------------------------------
        // 9. Invoices
        // -----------------------------------------------------------------------
        $lilyMurphy  = Child::where('first_name', 'Lily')->where('last_name', 'Murphy')->first();
        $oliverDavis = Child::where('first_name', 'Oliver')->where('last_name', 'Davis')->first();

        if ($miaKelly) {
            $this->makeInvoice($miaKelly, $parent, '2026-03-01', '2026-03-31', '2026-03-31', 850, 0, 'sent', [
                ['Full-day care Mon–Fri', 1, 800.00],
                ['Activity fee',          1,  50.00],
            ]);
            $this->makeInvoice($miaKelly, $parent, '2026-02-01', '2026-02-28', '2026-02-28', 850, 0, 'paid', [
                ['Full-day care Mon–Fri', 1, 800.00],
                ['Activity fee',          1,  50.00],
            ]);
        }

        if ($lilyMurphy) {
            $this->makeInvoice($lilyMurphy, $parent2, '2026-03-01', '2026-03-31', '2026-03-31', 650, 0, 'sent', [
                ['3-day care Mon/Wed/Fri', 1, 600.00],
                ['Art supplies',           1,  50.00],
            ], [
                'payment_status'       => 'payment_submitted',
                'payment_submitted_at' => now()->subDays(3),
                'payment_notes'        => 'Bank transfer receipt uploaded.',
            ]);
        }

        if ($oliverDavis && $parent3) {
            $this->makeInvoice($oliverDavis, $parent3, '2026-04-01', '2026-04-30', '2026-04-30', 900, 0, 'draft', [
                ['Full-day care Mon–Fri', 1, 850.00],
                ['Settling-in sessions',  1,  50.00],
            ]);
        }

        // -----------------------------------------------------------------------
        // 10. Acknowledgements
        // -----------------------------------------------------------------------

        // Pending — today's report for Mia Kelly
        $pendingReport = DailyReport::where('child_id', $miaKelly?->id)
            ->where('date', $today)
            ->first();
        if ($pendingReport) {
            Acknowledgement::updateOrCreate(
                ['record_type' => 'daily_report', 'record_id' => $pendingReport->id, 'parent_id' => $parent->id],
                ['status' => 'pending', 'signed_at' => null, 'signature_name' => null]
            );
        }

        // Acknowledged — an older report
        $signedReport = DailyReport::where('child_id', $miaKelly?->id)
            ->orderByDesc('date')
            ->skip(1)->first();
        if ($signedReport) {
            Acknowledgement::updateOrCreate(
                ['record_type' => 'daily_report', 'record_id' => $signedReport->id, 'parent_id' => $parent->id],
                ['status' => 'acknowledged', 'signed_at' => now()->subDays(2), 'signature_name' => $parent->name]
            );
        }

        // Incident acknowledgements
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

        $lowIncident = IncidentReport::where('severity', 'low')->first();
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
        // 11. Milestone observations — 5 children, ~40% of age-appropriate milestones
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

            foreach ($allChildren->take(5) as $child) {
                $ageMonths = Carbon::parse($child->dob)->diffInMonths(now());

                $relevant = $allMilestones->filter(function ($m) use ($ageMonths) {
                    [$min, $max] = explode('-', $m->age_range_months);
                    return $ageMonths >= (int) $min && $ageMonths <= ((int) $max + 6);
                });

                if ($relevant->isEmpty()) {
                    continue;
                }

                $count     = max(1, (int) ($relevant->count() * 0.4));
                $toObserve = $relevant->random($count);

                foreach ($toObserve as $milestone) {
                    DB::table('child_milestones')->updateOrInsert(
                        ['child_id' => $child->id, 'milestone_id' => $milestone->id],
                        [
                            'child_id'     => $child->id,
                            'milestone_id' => $milestone->id,
                            'observed_by'  => $carer->id,
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
        // 12. Messages
        // -----------------------------------------------------------------------

        // Thread 1: carer ↔ parent about Mia Kelly
        $this->seedThread(
            $carer, $parent, $miaKelly,
            [
                [$carer,  $parent,  "Hi! Just wanted to let you know {$miaKelly?->first_name} had a great day today. She's really settling in well."],
                [$parent, $carer,   "That's lovely to hear, thank you! She talks about crèche all the time at home."],
                [$carer,  $parent,  "She made a beautiful painting today, I'll put it in her bag. Also a reminder — we have pyjama day on Friday!"],
                [$parent, $carer,   "Oh brilliant, she'll love that! Thanks for letting me know."],
            ]
        );

        // Thread 2: parent2 ↔ carer about Noah Byrne
        $this->seedThread(
            $carer, $parent2, $noahByrne,
            [
                [$parent2, $carer,  "Hi, {$noahByrne?->first_name} has been a bit under the weather. Can you keep an eye on him today?"],
                [$carer,   $parent2,"Of course! I'll monitor him and let you know how he gets on."],
                [$carer,   $parent2,"Update: {$noahByrne?->first_name} perked up after lunch and played happily this afternoon. No temperature."],
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
