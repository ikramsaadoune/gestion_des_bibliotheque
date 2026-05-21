<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    protected array $categories = [
        [
            'name' => 'Fiction & Literature',
            'slug' => 'fiction-litterature',
            'description' => 'Romans, nouvelles et œuvres littéraires qui explorent l\'imaginaire, les émotions humaines et les récits captivants à travers les genres les plus variés, du classique au contemporain.',
            'icon' => 'fa-book-open',
            'color' => '#7C3AED',
        ],
        [
            'name' => 'Science & Mathematics',
            'slug' => 'sciences',
            'description' => 'Ouvrages scientifiques qui dévoilent les mystères de l\'univers, de la physique quantique à la biologie moléculaire, en passant par les mathématiques pures et appliquées.',
            'icon' => 'fa-flask',
            'color' => '#059669',
        ],
        [
            'name' => 'History & Geography',
            'slug' => 'histoire',
            'description' => 'Récits historiques et géographiques qui retracent le parcours des civilisations, des grands événements et des figures qui ont façonné notre monde à travers les âges.',
            'icon' => 'fa-landmark',
            'color' => '#D97706',
        ],
        [
            'name' => 'Technology & Computing',
            'slug' => 'technologie',
            'description' => 'Livres sur les avancées technologiques, l\'informatique, l\'intelligence artificielle et la transformation numérique qui révolutionnent notre société et notre quotidien.',
            'icon' => 'fa-microchip',
            'color' => '#DC2626',
        ],
        [
            'name' => 'Philosophy & Psychology',
            'slug' => 'philosophie',
            'description' => 'Réflexions profondes sur le sens de l\'existence, la conscience, l\'éthique et le fonctionnement de l\'esprit humain à travers les grands penseurs et courants philosophiques.',
            'icon' => 'fa-brain',
            'color' => '#0891B2',
        ],
        [
            'name' => 'Arts & Music',
            'slug' => 'arts',
            'description' => 'Exploration de la créativité humaine à travers la peinture, la musique, la sculpture, l\'architecture et toutes les formes d\'expression artistique qui enrichissent notre culture.',
            'icon' => 'fa-palette',
            'color' => '#DB2777',
        ],
        [
            'name' => 'Business & Economics',
            'slug' => 'business',
            'description' => 'Analyse des marchés, des stratégies d\'entreprise, des théories économiques et du management pour comprendre les mécanismes qui régissent l\'économie mondiale.',
            'icon' => 'fa-chart-line',
            'color' => '#2563EB',
        ],
        [
            'name' => 'Health & Medicine',
            'slug' => 'sante',
            'description' => 'Ouvrages médicaux et de santé publique qui abordent le corps humain, les maladies, les traitements et le bien-être pour une meilleure compréhension de la médecine.',
            'icon' => 'fa-heartbeat',
            'color' => '#0D9488',
        ],
        [
            'name' => 'Religion & Spirituality',
            'slug' => 'religion',
            'description' => 'Textes sacrés, spiritualité, croyances et pratiques religieuses qui explorent la dimension transcendante de l\'existence et la quête de sens universelle.',
            'icon' => 'fa-pray',
            'color' => '#4F46E5',
        ],
        [
            'name' => 'Education & Languages',
            'slug' => 'education',
            'description' => 'Manuels pédagogiques, méthodes d\'apprentissage des langues et ouvrages sur l\'enseignement qui facilitent la transmission du savoir et le développement des compétences.',
            'icon' => 'fa-graduation-cap',
            'color' => '#EA580C',
        ],
    ];

    public function run(): void
    {
        foreach ($this->categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['is_active' => true])
            );
        }
    }
}
