<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Enums\PublishingPhase;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class PublishingWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo users for different roles
        $director = User::firstOrCreate(
            ['email' => 'direktor@zalozba.si'],
            ['name' => 'Marko Novak', 'password' => bcrypt('geslo123')]
        );

        $chiefEditor = User::firstOrCreate(
            ['email' => 'glavni.urednik@zalozba.si'],
            ['name' => 'Ana Horvat', 'password' => bcrypt('geslo123')]
        );

        $editor1 = User::firstOrCreate(
            ['email' => 'urednik1@zalozba.si'],
            ['name' => 'Petra Kovač', 'password' => bcrypt('geslo123')]
        );

        $editor2 = User::firstOrCreate(
            ['email' => 'urednik2@zalozba.si'],
            ['name' => 'Miha Zupan', 'password' => bcrypt('geslo123')]
        );

        $multimedia = User::firstOrCreate(
            ['email' => 'multimedija@zalozba.si'],
            ['name' => 'Sara Novak', 'password' => bcrypt('geslo123')]
        );

        $print = User::firstOrCreate(
            ['email' => 'tisk@zalozba.si'],
            ['name' => 'Janez Kranjc', 'password' => bcrypt('geslo123')]
        );

        $sales = User::firstOrCreate(
            ['email' => 'prodaja@zalozba.si'],
            ['name' => 'Nina Vidmar', 'password' => bcrypt('geslo123')]
        );

        // Create customers
        $customer1 = Customer::firstOrCreate(
            ['name' => 'Ministrstvo za šolstvo'],
            [
                'type' => 'legal',
                'email' => 'narocila@mss.gov.si',
                'address' => 'Masarykova cesta 16',
                'postal_code' => '1000',
                'city' => 'Ljubljana',
            ]
        );

        $customer2 = Customer::firstOrCreate(
            ['name' => 'Osnovna šola Ljubljana Center'],
            [
                'type' => 'legal',
                'email' => 'info@oslc.si',
                'address' => 'Kardeljeva ploščad 28',
                'postal_code' => '1000',
                'city' => 'Ljubljana',
            ]
        );

        $customer3 = Customer::firstOrCreate(
            ['name' => 'Univerza v Ljubljani'],
            [
                'type' => 'legal',
                'email' => 'zalozba@uni-lj.si',
                'address' => 'Kongresni trg 12',
                'postal_code' => '1000',
                'city' => 'Ljubljana',
            ]
        );

        // Project 1: V urejanju
        $project1 = Project::create([
            'name' => 'Matematika za 5. razred - Delovni zvezek',
            'customer_id' => $customer1->id,
            'description' => 'Delovni zvezek za matematiko v 5. razredu osnovne šole. Vsebuje vaje in naloge iz programa.',
            'status' => ProjectStatus::InProgress,
            'current_phase' => PublishingPhase::Editing,
            'starts_at' => now()->subDays(15),
            'ends_at' => now()->addDays(75),
            'user_id' => $editor1->id,
        ]);

        Task::create([
            'project_id' => $project1->id,
            'title' => 'Pregled poglavja: Ulomki',
            'description' => 'Pregled in lektoriranje poglavja o ulomkih',
            'assigned_to' => $editor1->id,
            'assigned_by' => $chiefEditor->id,
            'status' => TaskStatus::InProgress,
            'priority' => TaskPriority::High,
            'due_at' => now()->addDays(3),
        ]);

        Task::create([
            'project_id' => $project1->id,
            'title' => 'Priprava nalog za vaje',
            'description' => 'Izdelava dodatnih nalog za utrditev snovi',
            'assigned_to' => $editor1->id,
            'assigned_by' => $chiefEditor->id,
            'status' => TaskStatus::Pending,
            'priority' => TaskPriority::Medium,
            'due_at' => now()->addDays(7),
        ]);

        // Project 2: Pregled glavnega urednika
        $project2 = Project::create([
            'name' => 'Slovenščina 8 - Učbenik',
            'customer_id' => $customer1->id,
            'description' => 'Učbenik za slovenščino v 8. razredu. Vključuje literarne izbore in jezikovne vsebine.',
            'status' => ProjectStatus::InProgress,
            'current_phase' => PublishingPhase::ChiefEditorReview,
            'starts_at' => now()->subDays(45),
            'ends_at' => now()->addDays(45),
            'user_id' => $editor2->id,
        ]);

        Task::create([
            'project_id' => $project2->id,
            'title' => 'Končni pregled besedil',
            'description' => 'Končna kontrola vseh besedil in avtorskih pravic',
            'assigned_to' => $chiefEditor->id,
            'assigned_by' => $chiefEditor->id,
            'status' => TaskStatus::InProgress,
            'priority' => TaskPriority::Urgent,
            'due_at' => now()->addDays(2),
        ]);

        // Project 3: Multimedijska obdelava
        $project3 = Project::create([
            'name' => 'Naravoslovje 6 - Interaktivni priročnik',
            'customer_id' => $customer2->id,
            'description' => 'Interaktivni priročnik za naravoslovje s 3D modeli in video vsebinami.',
            'status' => ProjectStatus::InProgress,
            'current_phase' => PublishingPhase::Multimedia,
            'starts_at' => now()->subDays(60),
            'ends_at' => now()->addDays(30),
            'user_id' => $multimedia->id,
        ]);

        Task::create([
            'project_id' => $project3->id,
            'title' => '3D model: Človeški srčni sistem',
            'description' => 'Izdelava 3D modela srčnega sistema s popisi',
            'assigned_to' => $multimedia->id,
            'assigned_by' => $chiefEditor->id,
            'status' => TaskStatus::InProgress,
            'priority' => TaskPriority::High,
            'due_at' => now()->addDays(5),
        ]);

        Task::create([
            'project_id' => $project3->id,
            'title' => 'Video: Fotosinteza',
            'description' => 'Izdelava kratkega video posnetka o fotosintetičnem procesu',
            'assigned_to' => $multimedia->id,
            'assigned_by' => $chiefEditor->id,
            'status' => TaskStatus::Pending,
            'priority' => TaskPriority::Medium,
            'due_at' => now()->addDays(10),
        ]);

        // Project 4: Tisk
        $project4 = Project::create([
            'name' => 'Zgodovina sveta - 9. razred',
            'customer_id' => $customer1->id,
            'description' => 'Učbenik iz zgodovine za 9. razred z barvnimi slikami in zemljevidi.',
            'status' => ProjectStatus::InProgress,
            'current_phase' => PublishingPhase::Print,
            'starts_at' => now()->subDays(80),
            'ends_at' => now()->addDays(10),
            'user_id' => $print->id,
        ]);

        Task::create([
            'project_id' => $project4->id,
            'title' => 'Kontrola barvnih odtisov',
            'description' => 'Pregled in potrditev barvnih odtisov pred končnim tiskom',
            'assigned_to' => $print->id,
            'assigned_by' => $chiefEditor->id,
            'status' => TaskStatus::InProgress,
            'priority' => TaskPriority::Urgent,
            'due_at' => now()->addDays(1),
        ]);

        // Project 5: Potrditev direktorja
        $project5 = Project::create([
            'name' => 'Ekonomija za srednje šole',
            'customer_id' => $customer3->id,
            'description' => 'Učbenik ekonomije za srednje šole z aktualnimi primeri iz slovenskega gospodarstva.',
            'status' => ProjectStatus::InProgress,
            'current_phase' => PublishingPhase::DirectorApproval,
            'starts_at' => now()->subDays(90),
            'ends_at' => now()->addDays(5),
            'user_id' => $director->id,
        ]);

        Task::create([
            'project_id' => $project5->id,
            'title' => 'Pregled pogodbe in cene',
            'description' => 'Končna potrditev pogodbe s stranko in cenovnih pogojev',
            'assigned_to' => $director->id,
            'assigned_by' => $director->id,
            'status' => TaskStatus::InProgress,
            'priority' => TaskPriority::High,
            'due_at' => now()->addDays(2),
        ]);

        // Project 6: Prodaja
        $project6 = Project::create([
            'name' => 'Angleščina 7 - Workbook',
            'customer_id' => $customer1->id,
            'description' => 'Delovni zvezek za angleščino v 7. razredu z avdio posnetki.',
            'status' => ProjectStatus::InProgress,
            'current_phase' => PublishingPhase::Sales,
            'starts_at' => now()->subDays(100),
            'ends_at' => now()->subDays(5),
            'user_id' => $sales->id,
        ]);

        Task::create([
            'project_id' => $project6->id,
            'title' => 'Promocijska kampanja',
            'description' => 'Priprava promocijskih materialov za sejme in šole',
            'assigned_to' => $sales->id,
            'assigned_by' => $director->id,
            'status' => TaskStatus::InProgress,
            'priority' => TaskPriority::Medium,
            'due_at' => now()->addDays(7),
        ]);

        // Project 7: Zaključen
        $project7 = Project::create([
            'name' => 'Fizika 1 - Mehanika',
            'customer_id' => $customer3->id,
            'description' => 'Prvi del učbenika fizike za 1. letnik srednje šole, vsebina: Mehanika.',
            'status' => ProjectStatus::Completed,
            'current_phase' => PublishingPhase::Completed,
            'starts_at' => now()->subDays(120),
            'ends_at' => now()->subDays(10),
            'user_id' => $editor1->id,
        ]);

        Task::create([
            'project_id' => $project7->id,
            'title' => 'Finalna dostava',
            'description' => 'Dostava naročenih primerkov na univerzo',
            'assigned_to' => $sales->id,
            'assigned_by' => $director->id,
            'status' => TaskStatus::Completed,
            'priority' => TaskPriority::High,
            'due_at' => now()->subDays(12),
            'completed_at' => now()->subDays(11),
        ]);
    }
}
