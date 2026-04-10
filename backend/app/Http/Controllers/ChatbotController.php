<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    /**
     * FAQ database grouped by category.
     */
    private function getFaqs(): array
    {
        return [
            // Opening Hours & General
            [
                'category' => 'General',
                'keywords' => ['hours', 'open', 'close', 'time', 'opening', 'closing'],
                'question' => 'What are your opening hours?',
                'answer' => 'SnugBug is open Monday to Friday, 7:30 AM to 6:00 PM. We are closed on weekends and public holidays.',
            ],
            [
                'category' => 'General',
                'keywords' => ['closed', 'holiday', 'bank holiday', 'christmas', 'easter'],
                'question' => 'When are you closed?',
                'answer' => "We are closed on all public/bank holidays, Christmas Eve through New Year's Day, and Easter weekend (Good Friday to Easter Monday). Parents are notified of closures at least 2 weeks in advance.",
            ],
            [
                'category' => 'General',
                'keywords' => ['age', 'ages', 'old', 'months', 'baby', 'toddler', 'accept', 'young'],
                'question' => 'What ages do you accept?',
                'answer' => 'We accept children from 6 months to 5 years of age. Our rooms are divided by age: Bumblebees (6-24 months) and Ladybirds (2-5 years).',
            ],
            [
                'category' => 'General',
                'keywords' => ['enrol', 'enroll', 'register', 'sign up', 'join', 'place', 'waiting', 'start', 'apply'],
                'question' => 'How do I enrol my child?',
                'answer' => "To enrol your child, please contact our centre manager through the Messages section of this app or call us directly. We'll arrange a visit so you and your child can see the crèche before starting. Places are subject to availability.",
            ],
            [
                'category' => 'General',
                'keywords' => ['contact', 'phone', 'email', 'call', 'reach', 'manager'],
                'question' => 'How can I contact the crèche?',
                'answer' => 'You can message the centre manager directly through the Messages section in the app. For urgent matters, please call the crèche directly. Our manager monitors messages throughout the day.',
            ],

            // Fees & Payments
            [
                'category' => 'Fees & Payments',
                'keywords' => ['fee', 'fees', 'cost', 'price', 'how much', 'payment', 'pay', 'invoice', 'charge'],
                'question' => 'How much does it cost?',
                'answer' => 'Our fees vary depending on the number of days and session type (full day or half day). You can view your invoices in the Invoices section of the app. Please contact the centre manager for a detailed fee schedule.',
            ],
            [
                'category' => 'Fees & Payments',
                'keywords' => ['ncs', 'subsidy', 'childcare scheme', 'government', 'funding', 'national childcare', 'grant'],
                'question' => 'Do you accept the National Childcare Scheme (NCS)?',
                'answer' => 'Yes! We are a registered NCS provider. The NCS subsidy is applied directly to your childcare bill. You can apply for the NCS at ncs.gov.ie using your MyGovID. If you need help, our manager can guide you through the process.',
            ],
            [
                'category' => 'Fees & Payments',
                'keywords' => ['ecce', 'free preschool', 'preschool year', 'free year', 'free place'],
                'question' => 'Do you offer the ECCE programme?',
                'answer' => 'Yes, we participate in the ECCE (Early Childhood Care and Education) programme, which provides two years of free preschool for children aged 2 years 8 months to 5 years 6 months. Please ask our manager about eligibility and availability.',
            ],
            [
                'category' => 'Fees & Payments',
                'keywords' => ['late', 'late fee', 'collect late', 'after 6', 'overtime'],
                'question' => 'Is there a late collection fee?',
                'answer' => 'Yes, a late collection fee applies after 6:00 PM. Please notify us as soon as possible if you are running late. Persistent late collections may be subject to additional charges as outlined in your service agreement.',
            ],

            // Daily Routine & Care
            [
                'category' => 'Daily Routine',
                'keywords' => ['food', 'meal', 'meals', 'lunch', 'breakfast', 'snack', 'eat', 'menu', 'diet', 'nutrition'],
                'question' => 'What meals do you provide?',
                'answer' => "We provide a nutritious breakfast, morning snack, hot lunch, and afternoon snack daily. Our menu follows the Department of Health's nutrition guidelines for early years. All meals are prepared fresh on-site. We cater for allergies and dietary requirements — please make sure your child's allergies are updated in the app.",
            ],
            [
                'category' => 'Daily Routine',
                'keywords' => ['allergy', 'allergies', 'allergic', 'intolerance', 'nut', 'peanut', 'dairy', 'gluten', 'coeliac'],
                'question' => 'How do you handle allergies?',
                'answer' => "Child safety is our top priority. All allergies are recorded in our system and visible to carers on every screen — attendance, meals, and medication. A bright red allergy alert banner appears whenever a carer interacts with a child who has allergies. Please keep your child's allergy information up to date through the centre manager.",
            ],
            [
                'category' => 'Daily Routine',
                'keywords' => ['nap', 'sleep', 'rest', 'naps', 'sleeping', 'tired'],
                'question' => 'What is the nap/sleep routine?',
                'answer' => "Each child has an individual sleep routine based on their age and needs. Bumblebees (babies/toddlers) have morning and afternoon nap times. Ladybirds (older children) have a rest period after lunch. You can see your child's sleep times in the Daily Timeline on the app.",
            ],
            [
                'category' => 'Daily Routine',
                'keywords' => ['curriculum', 'aistear', 'learning', 'education', 'milestone', 'development', 'siolta'],
                'question' => 'What curriculum do you follow?',
                'answer' => "We follow the Aistear early childhood curriculum framework, Ireland's national framework for early learning. It covers four themes: Well-being, Identity & Belonging, Communicating, and Exploring & Thinking. You can track your child's developmental milestones through the Milestones section of the app.",
            ],
            [
                'category' => 'Daily Routine',
                'keywords' => ['activity', 'activities', 'play', 'outdoor', 'garden', 'art', 'music', 'sport', 'craft'],
                'question' => 'What activities do you offer?',
                'answer' => 'Our daily programme includes free play, structured activities, art and crafts, music and movement, storytime, outdoor play, sensory exploration, and physical activities. Activities are age-appropriate and linked to the Aistear curriculum themes.',
            ],
            [
                'category' => 'Daily Routine',
                'keywords' => ['routine', 'schedule', 'typical day', 'daily', 'timetable', 'day look like'],
                'question' => 'What does a typical day look like?',
                'answer' => "A typical day includes: 7:30-9:00 Arrival & free play, 9:00-9:30 Breakfast, 9:30-11:00 Structured activities, 11:00-11:30 Outdoor play, 11:30-12:00 Lunch, 12:00-2:00 Nap/quiet time, 2:00-3:00 Afternoon activities, 3:00-3:30 Snack, 3:30-6:00 Free play & collection. You can track your child's actual day through the Timeline in the app.",
            ],

            // Safety & Policies
            [
                'category' => 'Safety & Policies',
                'keywords' => ['sick', 'ill', 'unwell', 'temperature', 'fever', 'vomit', 'diarrhoea'],
                'question' => 'What is your sickness policy?',
                'answer' => "Children must be kept at home if they have a temperature (38°C+), vomiting, diarrhoea, or any contagious illness. Children must be symptom-free for 48 hours before returning. If your child becomes unwell during the day, we will contact you immediately.",
            ],
            [
                'category' => 'Safety & Policies',
                'keywords' => ['medicine', 'medication', 'calpol', 'inhaler', 'administer', 'prescribe'],
                'question' => 'Can you administer medication?',
                'answer' => 'Yes, we can administer prescribed medication with written parental consent. All medication must be in its original packaging with clear dosage instructions. Every dose is logged in the app so you can see exactly when medication was given.',
            ],
            [
                'category' => 'Safety & Policies',
                'keywords' => ['pickup', 'pick up', 'collect', 'collection', 'authorised', 'authorized', 'who can collect'],
                'question' => 'Who can collect my child?',
                'answer' => 'Only authorised persons listed on your registration form can collect your child. If someone different is collecting, please notify us in advance via the app or by phone. We will ask for photo ID if we do not recognise the person.',
            ],
            [
                'category' => 'Safety & Policies',
                'keywords' => ['incident', 'accident', 'hurt', 'injury', 'fall', 'bump', 'report'],
                'question' => 'What happens if my child has an accident?',
                'answer' => 'All incidents are documented in an Incident Report which you can view in the app. For minor incidents (bumps, scrapes), first aid is applied and you are notified at collection. For anything more serious, we contact you immediately. All incidents are reviewed by management.',
            ],
            [
                'category' => 'Safety & Policies',
                'keywords' => ['emergency', 'fire', 'drill', 'evacuation', 'lockdown'],
                'question' => 'What are your emergency procedures?',
                'answer' => 'We conduct regular fire drills and have a comprehensive emergency plan. All staff are first-aid trained. Emergency contact details are kept on file for every child. In case of an emergency closure, all parents are notified immediately through the app.',
            ],
            [
                'category' => 'Safety & Policies',
                'keywords' => ['bring', 'pack', 'need', 'supplies', 'nappies', 'diapers', 'clothes', 'bag', 'bottle'],
                'question' => 'What should my child bring?',
                'answer' => 'Please send your child with: a full change of clothes (labelled), nappies/pull-ups if needed, a comforter/soother if used, sun cream (summer), rain gear, and any prescribed medication. All items should be clearly labelled with your child\'s name.',
            ],

            // Using the App
            [
                'category' => 'Using the App',
                'keywords' => ['app', 'message', 'messages', 'chat', 'communicate', 'talk to carer'],
                'question' => 'How do I message my child\'s carer?',
                'answer' => 'Go to the Messages section in the app. You can start a new conversation with any carer or the centre manager. You\'ll also receive a notification badge when you have unread messages.',
            ],
            [
                'category' => 'Using the App',
                'keywords' => ['timeline', 'day', 'update', 'what did', 'today', 'feed', 'daily'],
                'question' => 'How do I see my child\'s day?',
                'answer' => 'Check the Timeline section — it shows a beautiful chronological feed of your child\'s entire day including meals, naps, activities, and any incidents. You can browse previous days using the date picker.',
            ],
            [
                'category' => 'Using the App',
                'keywords' => ['milestone', 'progress', 'development', 'tracker', 'aistear tracker'],
                'question' => 'How do I track my child\'s development?',
                'answer' => 'Go to the Milestones section to see your child\'s progress across the four Aistear themes: Well-being, Identity & Belonging, Communicating, and Exploring & Thinking. Carers record milestones as they observe them, and you can see progress bars for each category.',
            ],
            [
                'category' => 'Using the App',
                'keywords' => ['acknowledge', 'sign', 'signature', 'confirm', 'read'],
                'question' => 'What are acknowledgements?',
                'answer' => 'When the crèche sends you an important document (like a daily report or incident report), you may be asked to acknowledge it. Go to your dashboard or the Acknowledgements section, review the document, type your name as a signature, and confirm. This lets the crèche know you\'ve seen it.',
            ],
            [
                'category' => 'Using the App',
                'keywords' => ['invoice', 'bill', 'payment', 'view invoice', 'download'],
                'question' => 'How do I view my invoices?',
                'answer' => 'Go to the Invoices section in the app. You can see all your invoices, their status (draft, sent, paid), and the breakdown of charges. If you have questions about a specific invoice, message the centre manager.',
            ],
        ];
    }

    /**
     * Handle a chatbot message and return matching FAQ response.
     */
    public function respond(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage = strtolower(trim($request->message));

        // Handle greetings
        $greetings = ['hi', 'hello', 'hey', 'hiya', 'howya', 'good morning', 'good afternoon', 'help', 'helo'];
        foreach ($greetings as $greeting) {
            if (str_contains($userMessage, $greeting) && strlen($userMessage) < 20) {
                return response()->json([
                    'matched' => true,
                    'question' => null,
                    'answer' => "Hi there! 👋 I'm SnugBot, your SnugBug help assistant. I can answer questions about:\n\n📋 Opening hours & enrolment\n💶 Fees, NCS & ECCE\n🍽️ Meals, allergies & daily routine\n🏥 Sickness & medication policies\n📱 Using the app (timeline, milestones, messages)\n\nWhat would you like to know?",
                    'category' => null,
                    'related' => [],
                ]);
            }
        }

        // Handle thank you / goodbye
        $thanks = ['thank', 'thanks', 'cheers', 'brilliant', 'great', 'perfect', 'bye', 'goodbye'];
        foreach ($thanks as $word) {
            if (str_contains($userMessage, $word) && strlen($userMessage) < 30) {
                return response()->json([
                    'matched' => true,
                    'question' => null,
                    'answer' => "You're welcome! 😊 If you have any other questions, I'm here to help. You can also message the centre manager directly through the Messages section for anything specific to your child.",
                    'category' => null,
                    'related' => [],
                ]);
            }
        }

        // Common misspelling corrections
        $corrections = [
            'alergy' => 'allergy', 'alergies' => 'allergies', 'alergic' => 'allergic',
            'milstones' => 'milestones', 'millstones' => 'milestones',
            'medicin' => 'medicine', 'medecine' => 'medicine',
            'regestration' => 'registration', 'registeration' => 'registration',
            'scedule' => 'schedule', 'shedule' => 'schedule',
            'breakfest' => 'breakfast', 'brekfast' => 'breakfast',
            'colect' => 'collect', 'colection' => 'collection',
            'enviroment' => 'environment',
            'creche' => 'crèche', 'cresh' => 'crèche',
            'timline' => 'timeline',
        ];

        foreach ($corrections as $wrong => $right) {
            $userMessage = str_replace($wrong, $right, $userMessage);
        }

        $faqs = $this->getFaqs();
        $matches = [];

        foreach ($faqs as $faq) {
            $score = 0;
            foreach ($faq['keywords'] as $keyword) {
                $keyword = strtolower($keyword);
                if (str_contains($userMessage, $keyword)) {
                    // Multi-word keywords get a higher score
                    $score += str_word_count($keyword) > 1 ? 3 : 1;
                }
            }
            if ($score > 0) {
                $matches[] = array_merge($faq, ['score' => $score]);
            }
        }

        // Sort by score descending — best match first
        usort($matches, fn($a, $b) => $b['score'] <=> $a['score']);

        if (!empty($matches)) {
            $best = $matches[0];
            $related = array_slice($matches, 1, 2); // up to 2 related questions

            return response()->json([
                'matched' => true,
                'question' => $best['question'],
                'answer' => $best['answer'],
                'category' => $best['category'],
                'related' => array_map(fn($m) => [
                    'question' => $m['question'],
                    'category' => $m['category'],
                ], $related),
            ]);
        }

        // Fallback — no match found
        return response()->json([
            'matched' => false,
            'question' => null,
            'answer' => "I'm not sure about that one! Try asking something simpler, like:\n\n• \"What are your opening hours?\"\n• \"How much does it cost?\"\n• \"How do you handle allergies?\"\n• \"What should my child bring?\"\n• \"How do I see my child's day?\"\n\nOr message the centre manager directly for specific questions about your child.",
            'category' => null,
            'related' => [],
        ]);
    }

    /**
     * Return suggested questions grouped by category.
     */
    public function suggestions()
    {
        $faqs = $this->getFaqs();
        $grouped = [];

        foreach ($faqs as $faq) {
            $grouped[$faq['category']][] = $faq['question'];
        }

        return response()->json([
            'categories' => $grouped,
        ]);
    }
}
