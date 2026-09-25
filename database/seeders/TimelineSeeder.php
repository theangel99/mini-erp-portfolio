<?php

namespace Database\Seeders;

use App\Enums\MilestoneStatus;
use App\Enums\ProjectStatus;
use App\Enums\WaitingOn;
use App\Models\Customer;
use App\Models\Deadline;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class TimelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create demo user
        $demoUser = User::where('email', 'admin@veberdigital.com')->first()
            ?? User::factory()->create(['email' => 'admin@veberdigital.com']);

        // Project 1: Prenova spletne strani – Mizarstvo Kovač (waiting na stranko 9 dni)
        $project1 = Project::factory()->for($demoUser, 'user')->create([
            'name' => 'Prenova spletne strani – Mizarstvo Kovač',
            'customer_id' => Customer::factory()->create(['name' => 'Mizarstvo Kovač'])->id,
            'status' => ProjectStatus::InProgress,
            'starts_at' => now()->subDays(40),
            'ends_at' => now()->addDays(20),
        ]);

        $this->createMilestonesForProject1($project1);

        // Project 2: Interni sistem za naročila – Avtoservis Zupan (zamuja)
        $project2 = Project::factory()->for($demoUser, 'user')->create([
            'name' => 'Interni sistem za naročila – Avtoservis Zupan',
            'customer_id' => Customer::factory()->create(['name' => 'Avtoservis Zupan'])->id,
            'status' => ProjectStatus::InProgress,
            'starts_at' => now()->subDays(60),
            'ends_at' => now()->subDays(5),
        ]);

        $this->createMilestonesForProject2($project2);

        // Project 3: Spletna trgovina – Čebelarstvo Novak (skoraj zaključen)
        $project3 = Project::factory()->for($demoUser, 'user')->create([
            'name' => 'Spletna trgovina – Čebelarstvo Novak',
            'customer_id' => Customer::factory()->create(['name' => 'Čebelarstvo Novak'])->id,
            'status' => ProjectStatus::InProgress,
            'starts_at' => now()->subDays(50),
            'ends_at' => now()->addDays(10),
        ]);

        $this->createMilestonesForProject3($project3);

        // Create deadlines for demo user
        $this->createDeadlinesForDemoUser($demoUser, $project1, $project2, $project3);
    }

    private function createMilestonesForProject1(Project $project): void
    {
        Milestone::factory()->for($project)->done(35)->create(['title' => 'Analiza zahtev', 'sort' => 1]);
        Milestone::factory()->for($project)->done(28)->create(['title' => 'Dizajn vmesnika', 'sort' => 2]);
        Milestone::factory()->for($project)->done(20)->create(['title' => 'Potrditev stranke - dizajn', 'sort' => 3]);
        Milestone::factory()->for($project)->done(10)->create(['title' => 'Razvoj osnovnih funkcionalnosti', 'sort' => 4]);
        Milestone::factory()->for($project)->waiting(WaitingOn::Customer, 9)->create([
            'title' => 'Potrditev stranke - vsebina',
            'sort' => 5,
            'waiting_note' => 'Čakamo na fotografije in besedila od stranke',
        ]);
        Milestone::factory()->for($project)->create([
            'title' => 'Testiranje in popravki',
            'sort' => 6,
            'status' => MilestoneStatus::Upcoming,
            'planned_at' => now()->addDays(5),
        ]);
        Milestone::factory()->for($project)->create([
            'title' => 'Predaja in objava',
            'sort' => 7,
            'status' => MilestoneStatus::Upcoming,
            'planned_at' => now()->addDays(18),
        ]);
    }

    private function createMilestonesForProject2(Project $project): void
    {
        Milestone::factory()->for($project)->done(55)->create(['title' => 'Zajem potreb in analiza', 'sort' => 1]);
        Milestone::factory()->for($project)->done(40)->create(['title' => 'UX dizajn sistema', 'sort' => 2]);
        Milestone::factory()->for($project)->done(25)->create(['title' => 'Razvoj modula za naročila', 'sort' => 3]);
        Milestone::factory()->for($project)->done(12)->create(['title' => 'Razvoj modula za stranke', 'sort' => 4]);
        Milestone::factory()->for($project)->inProgress()->create([
            'title' => 'Testiranje in odpravljanje napak',
            'sort' => 5,
            'planned_at' => now()->subDays(5),
        ]);
        Milestone::factory()->for($project)->create([
            'title' => 'Migracija podatkov',
            'sort' => 6,
            'status' => MilestoneStatus::Upcoming,
            'planned_at' => now()->addDays(5),
        ]);
        Milestone::factory()->for($project)->create([
            'title' => 'Predaja in usposabljanje',
            'sort' => 7,
            'status' => MilestoneStatus::Upcoming,
            'planned_at' => now()->addDays(12),
        ]);
    }

    private function createMilestonesForProject3(Project $project): void
    {
        Milestone::factory()->for($project)->done(45)->create(['title' => 'Načrtovanje arhitekture', 'sort' => 1]);
        Milestone::factory()->for($project)->done(38)->create(['title' => 'Dizajn spletne trgovine', 'sort' => 2]);
        Milestone::factory()->for($project)->done(30)->create(['title' => 'Razvoj kataloga izdelkov', 'sort' => 3]);
        Milestone::factory()->for($project)->done(20)->create(['title' => 'Integracija plačilnega sistema', 'sort' => 4]);
        Milestone::factory()->for($project)->done(10)->create(['title' => 'Razvoj administratorskega vmesnika', 'sort' => 5]);
        Milestone::factory()->for($project)->inProgress()->create([
            'title' => 'Zaključno testiranje',
            'sort' => 6,
            'planned_at' => now()->subDays(2),
        ]);
        Milestone::factory()->for($project)->create([
            'title' => 'Lansiranje in monitoring',
            'sort' => 7,
            'status' => MilestoneStatus::Upcoming,
            'planned_at' => now()->addDays(8),
        ]);
    }

    private function createDeadlinesForDemoUser(User $user, Project $project1, Project $project2, Project $project3): void
    {
        // Do jutri: 3 roki (1 danes, 2 jutri)
        Deadline::factory()->for($user)->dueToday()->create(['title' => 'Pregled ponudbe za nov projekt']);
        Deadline::factory()->for($user)->dueTomorrow()->create(['title' => 'Priprava fakture za projekt Kovač', 'project_id' => $project1->id]);
        Deadline::factory()->for($user)->dueTomorrow()->create(['title' => 'Sestanek s stranko Zupan', 'project_id' => $project2->id]);

        // Ta teden: 3 roki
        Deadline::factory()->for($user)->dueThisWeek()->create(['title' => 'Oddaja mesečnega poročila']);
        Deadline::factory()->for($user)->dueThisWeek()->create(['title' => 'Testiranje funkcionalnosti košarice', 'project_id' => $project3->id]);
        Deadline::factory()->for($user)->dueThisWeek()->create(['title' => 'Posodobitev strežniških certifikatov']);

        // Naslednji mesec: 2 roka
        Deadline::factory()->for($user)->dueNextMonth()->create(['title' => 'Plačilo letne naročnine za hosting']);
        Deadline::factory()->for($user)->dueNextMonth()->create(['title' => 'Pregled letnih ciljev s ekipo']);

        // Kasneje: 2 roka
        Deadline::factory()->for($user)->create([
            'title' => 'Načrtovanje nove funkcionalnosti za Q2',
            'due_at' => now()->addDays(70),
        ]);
        Deadline::factory()->for($user)->create([
            'title' => 'Priprava na konferenco WebExpo',
            'due_at' => now()->addDays(90),
        ]);

        // Zapadel: 1 rok
        Deadline::factory()->for($user)->overdue()->create(['title' => 'Odgovor na vprašanja stranke']);

        // Opravljeno v tem polletju: 8 rokov (6 pravočasno, 2 z zamudo)
        $currentHalf = now()->month <= 6 ? 1 : 2;
        $halfStartMonth = $currentHalf === 1 ? 1 : 7;
        $halfStartDate = now()->setMonth($halfStartMonth)->setDay(1)->startOfDay();

        // 6 pravočasno
        for ($i = 0; $i < 6; $i++) {
            $dueDate = $halfStartDate->copy()->addDays(rand(1, now()->diffInDays($halfStartDate) - 1));
            Deadline::factory()->for($user)->completed(0)->create([
                'title' => 'Opravljeno: '.fake()->words(3, true),
                'due_at' => $dueDate,
            ]);
        }

        // 2 z zamudo
        for ($i = 0; $i < 2; $i++) {
            $dueDate = $halfStartDate->copy()->addDays(rand(1, now()->diffInDays($halfStartDate) - 5));
            Deadline::factory()->for($user)->completed(rand(1, 5))->create([
                'title' => 'Zamujeno: '.fake()->words(3, true),
                'due_at' => $dueDate,
            ]);
        }
    }
}
