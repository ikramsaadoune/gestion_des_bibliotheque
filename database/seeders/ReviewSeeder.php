<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();
        $bookIds = Book::pluck('id')->toArray();

        $reviews = [
            [
                'rating' => 5,
                'comment' => "Un chef-d'œuvre intemporel. Victor Hugo nous livre une fresque sociale d'une puissance inégalée. Chaque personnage est profondément humain et l'histoire de Jean Valjean est bouleversante.",
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'comment' => "Orwell avait une vision prophétique. Ce livre est plus pertinent que jamais à l'ère de la surveillance de masse et des fake news. Une lecture essentielle.",
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'comment' => "Un livre qui semble simple mais qui contient une sagesse infinie. Chaque relecture apporte de nouvelles découvertes. À offrir à tous les âges.",
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'comment' => "Le réalisme magique à son apogée. Garcia Marquez crée un monde si vivant et si poétique qu'on ne peut plus le quitter. Un des plus grands romans du XXe siècle.",
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'comment' => "Une satire sociale pleine d'esprit et d'ironie. Elizabeth Bennet est l'une des héroïnes les plus attachantes de la littérature. Austen avait un talent rare pour dépeindre les travers de la société.",
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'comment' => "Murakami mêle avec brio le réel et l'irréel. Son écriture poétique et onirique nous emmène dans un voyage intérieur fascinant. Parfois déroutant mais toujours captivant.",
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'comment' => "Hawking rend la physique accessible à tous. Ses explications sur les trous noirs et la relativité sont d'une clarté remarquable. Un livre qui change notre vision de l'univers.",
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'comment' => "Sapiens a complètement changé ma façon de voir l'histoire humaine. Harari écrit avec une clarté et une érudition impressionnantes. Un livre qui devrait être lu par tout le monde.",
                'is_approved' => true,
            ],
            [
                'rating' => 3,
                'comment' => "Intéressant mais un peu long à mon goût. Certaines parties sont fascinantes, d'autres traînent en longueur. Une bonne synthèse historique malgré tout.",
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'comment' => "Yourcenar écrit avec une beauté et une profondeur rares. Hadrien prend vie sous sa plume avec une humanité bouleversante. Un exercice d'empathie historique prodigieux.",
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'comment' => "Le Prophète est un livre de sagesse universelle. Chaque page offre une méditation profonde sur la vie, l'amour et la mort. Un compagnon pour toute une vie.",
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'comment' => "La Peste est bien plus qu'un roman sur une épidémie. C'est une réflexion sur l'engagement, la solidarité et ce qui donne un sens à notre existence. Très actuel.",
                'is_approved' => true,
            ],
            [
                'rating' => 3,
                'comment' => "L'Alchimiste est une jolie fable mais je la trouve un peu trop simpliste. Le message est beau mais répétitif. Cela reste une lecture agréable et inspirante pour certains.",
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'comment' => "Piketty propose une analyse économique rigoureuse et accessible. Ses données sur les inégalités sont accablantes. Un livre important pour comprendre les défis de notre époque.",
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'comment' => "Kahneman nous fait découvrir les rouages fascinants de notre cerveau. Ce livre m'a aidé à comprendre mes propres biais et à mieux prendre des décisions. Absolument passionnant.",
                'is_approved' => true,
            ],
            [
                'rating' => 2,
                'comment' => "Je n'ai pas accroché à ce livre. Le style est trop dense et l'histoire traîne en longueur. Peut-être que je n'étais pas dans le bon état d'esprit pour l'apprécier.",
                'is_approved' => false,
            ],
            [
                'rating' => 4,
                'comment' => "Un témoignage poignant sur la colonisation et ses effets dévastateurs. Achebe écrit avec une force et une dignité qui forcent le respect. Une lecture nécessaire.",
                'is_approved' => true,
            ],
            [
                'rating' => 1,
                'comment' => "Je n'ai pas du tout aimé. L'histoire est ennuyeuse et les personnages sont plats. Je ne comprends pas l'engouement autour de ce livre.",
                'is_approved' => false,
            ],
            [
                'rating' => 5,
                'comment' => "Le Mythe de Sisyphe est une lecture fondatrice. Camus nous apprend à trouver du sens même dans l'absurdité du monde. Un livre qui libère et qui donne de la force.",
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'comment' => "Maalouf offre une perspective rafraîchissante et nécessaire sur les Croisades. Son travail de recherche est impressionnant et son écriture est fluide et captivante.",
                'is_approved' => true,
            ],
        ];

        $usedPairs = [];

        foreach ($reviews as $index => $reviewData) {
            $userId = $userIds[$index % count($userIds)];
            $bookId = $bookIds[$index % count($bookIds)];
            $pairKey = $userId . '-' . $bookId;

            if (isset($usedPairs[$pairKey])) {
                $userId = $userIds[array_rand($userIds)];
                $bookId = $bookIds[array_rand($bookIds)];
                $pairKey = $userId . '-' . $bookId;
                if (isset($usedPairs[$pairKey])) {
                    continue;
                }
            }
            $usedPairs[$pairKey] = true;

            Review::create([
                'user_id' => $userId,
                'book_id' => $bookId,
                'rating' => $reviewData['rating'],
                'comment' => $reviewData['comment'],
                'is_approved' => $reviewData['is_approved'],
            ]);
        }
    }
}
