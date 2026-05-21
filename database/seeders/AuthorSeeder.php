<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuthorSeeder extends Seeder
{
    protected array $authors = [
        [
            'name' => 'Victor Hugo',
            'biography' => 'Écrivain, poète et dramaturge français considéré comme l\'un des plus grands auteurs de la littérature française. Figure majeure du romantisme, il a marqué son époque par ses engagements politiques et ses œuvres monumentales comme Les Misérables et Notre-Dame de Paris.',
            'birth_date' => '1802-02-26',
            'nationality' => 'français',
        ],
        [
            'name' => 'George Orwell',
            'biography' => 'Écrivain et essayiste britannique connu pour ses romans dystopiques 1984 et La Ferme des animaux. Ses œuvres dénoncent le totalitarisme et les dérives du pouvoir politique avec une lucidité prophétique.',
            'birth_date' => '1903-06-25',
            'nationality' => 'britannique',
        ],
        [
            'name' => 'Antoine de Saint-Exupéry',
            'biography' => 'Aviateur et écrivain français dont l\'œuvre célèbre l\'aviation, l\'amitié et l\'humanisme. Son chef-d\'œuvre Le Petit Prince est l\'un des livres les plus traduits au monde, vendu à plus de 200 millions d\'exemplaires.',
            'birth_date' => '1900-06-29',
            'nationality' => 'français',
        ],
        [
            'name' => 'J.K. Rowling',
            'biography' => 'Romancière britannique devenue célèbre grâce à la saga Harry Potter, l\'une des séries les plus vendues de l\'histoire avec plus de 500 millions d\'exemplaires. Ses livres ont inspiré une génération de lecteurs à travers le monde.',
            'birth_date' => '1965-07-31',
            'nationality' => 'britannique',
        ],
        [
            'name' => 'Paulo Coelho',
            'biography' => 'Écrivain brésilien dont le roman L\'Alchimiste est devenu un phénomène mondial avec plus de 150 millions d\'exemplaires vendus. Ses œuvres mêlent spiritualité, philosophie et quête de soi.',
            'birth_date' => '1947-08-24',
            'nationality' => 'brésilien',
        ],
        [
            'name' => 'Gabriel Garcia Marquez',
            'biography' => 'Romancier colombien, prix Nobel de littérature en 1982, considéré comme le maître du réalisme magique. Son chef-d\'œuvre Cent Ans de Solitude est un monument de la littérature mondiale qui a révolutionné la narration latino-américaine.',
            'birth_date' => '1927-03-06',
            'nationality' => 'colombien',
        ],
        [
            'name' => 'Albert Camus',
            'biography' => 'Écrivain, philosophe et journaliste français, prix Nobel de littérature en 1957. Figure majeure de l\'existentialisme et de la philosophie de l\'absurde, il a marqué la pensée du XXe siècle avec L\'Étranger, La Peste et Le Mythe de Sisyphe.',
            'birth_date' => '1913-11-07',
            'nationality' => 'français',
        ],
        [
            'name' => 'Jane Austen',
            'biography' => 'Romancière britannique dont les œuvres, parmi les plus lues de la littérature anglaise, analysent avec ironie et finesse les mœurs de la société georgienne. Orgueil et Préjugés reste son livre le plus célèbre et adapté.',
            'birth_date' => '1775-12-16',
            'nationality' => 'britannique',
        ],
        [
            'name' => 'Haruki Murakami',
            'biography' => 'Écrivain japonais contemporain connu pour son style onirique mêlant réalisme et fantastique. Ses romans, traduits dans plus de 50 langues, explorent la solitude, la mémoire et les mondes parallèles avec une originalité incomparable.',
            'birth_date' => '1949-01-12',
            'nationality' => 'japonais',
        ],
        [
            'name' => 'Umberto Eco',
            'biography' => 'Philosophe, sémiologue et romancier italien de renommée internationale. Son roman Le Nom de la Rose, best-seller mondial adapté au cinéma, mêle avec virtuosité enquête policière, histoire médiévale et réflexion philosophique.',
            'birth_date' => '1932-01-05',
            'nationality' => 'italien',
        ],
        [
            'name' => 'Khalil Gibran',
            'biography' => 'Poète, peintre et philosophe libanais, figure emblématique de la littérature arabe moderne. Son livre Le Prophète, recueil de poèmes philosophiques sur la vie et l\'amour, est traduit dans plus de 100 langues et vendu à des millions d\'exemplaires.',
            'birth_date' => '1883-01-06',
            'nationality' => 'libanais',
        ],
        [
            'name' => 'Léopold Sédar Senghor',
            'biography' => 'Poète, écrivain et homme d\'État sénégalais, cofondateur du mouvement de la Négritude. Premier président du Sénégal, il a laissé une œuvre poétique majeure qui célèbre la culture africaine et l\'universalisme.',
            'birth_date' => '1906-10-09',
            'nationality' => 'sénégalais',
        ],
        [
            'name' => 'Marguerite Yourcenar',
            'biography' => 'Romancière française, première femme élue à l\'Académie française. Ses romans historiques d\'une grande érudition, dont Mémoires d\'Hadrien, explorent la condition humaine à travers les siècles avec une profondeur philosophique remarquable.',
            'birth_date' => '1903-06-08',
            'nationality' => 'française',
        ],
        [
            'name' => 'Chinua Achebe',
            'biography' => 'Romancier et poète nigérian, considéré comme le père de la littérature africaine moderne. Son roman Le Monde s\'Effondre, vendu à plus de 20 millions d\'exemplaires, raconte la destruction des cultures traditionnelles africaines par le colonialisme.',
            'birth_date' => '1930-11-16',
            'nationality' => 'nigérian',
        ],
        [
            'name' => 'Amin Maalouf',
            'biography' => 'Écrivain franco-libanais, membre de l\'Académie française. Ses romans et essais historiques, comme Les Croisades vues par les Arabes, tissent avec talent les liens entre l\'Orient et l\'Occident à travers les époques.',
            'birth_date' => '1949-02-25',
            'nationality' => 'libanais',
        ],
        [
            'name' => 'Stephen Hawking',
            'biography' => 'Physicien théoricien et cosmologiste britannique de génie, connu pour ses travaux sur les trous noirs et la relativité. Son livre Une Brève Histoire du Temps a rendu la science accessible au grand public et s\'est vendu à plus de 10 millions d\'exemplaires.',
            'birth_date' => '1942-01-08',
            'nationality' => 'britannique',
        ],
        [
            'name' => 'Yuval Noah Harari',
            'biography' => 'Historien et professeur israélien, auteur du best-seller mondial Sapiens. Ses livres, qui retracent l\'histoire de l\'humanité et explorent son avenir, ont été traduits dans plus de 60 langues et vendus à plus de 20 millions d\'exemplaires.',
            'birth_date' => '1976-02-24',
            'nationality' => 'israélien',
        ],
        [
            'name' => 'Thomas Piketty',
            'biography' => 'Économiste français, professeur à l\'École d\'économie de Paris et directeur d\'études à l\'EHESS. Son livre Le Capital au XXIe Siècle est devenu un phénomène éditorial mondial en renouvelant l\'analyse des inégalités économiques.',
            'birth_date' => '1971-05-07',
            'nationality' => 'français',
        ],
        [
            'name' => 'Daniel Kahneman',
            'biography' => 'Psychologue israélo-américain, prix Nobel d\'économie 2002 pour ses travaux sur la prise de décision et les biais cognitifs. Son livre Thinking, Fast and Slow synthétise des décennies de recherche sur le fonctionnement de l\'esprit humain.',
            'birth_date' => '1934-03-05',
            'nationality' => 'israélien',
        ],
        [
            'name' => 'René Descartes',
            'biography' => 'Philosophe, mathématicien et scientifique français, considéré comme le père de la philosophie moderne. Sa méthode du doute radical et son célèbre Cogito ergo sum ont posé les fondements de la pensée rationaliste occidentale.',
            'birth_date' => '1596-03-31',
            'nationality' => 'français',
        ],
    ];

    public function run(): void
    {
        foreach ($this->authors as $authorData) {
            Author::firstOrCreate(
                ['slug' => Str::slug($authorData['name'])],
                [
                    'name' => $authorData['name'],
                    'slug' => Str::slug($authorData['name']),
                    'biography' => $authorData['biography'],
                    'photo' => null,
                    'birth_date' => $authorData['birth_date'],
                    'nationality' => $authorData['nationality'],
                    'is_active' => true,
                ]
            );
        }
    }
}
