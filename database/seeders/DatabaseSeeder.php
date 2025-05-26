<?php

namespace Database\Seeders;

use App\Models\Film;
use App\Models\FilmGenre;
use App\Models\FilmStatus;
use App\Models\Genre;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    private readonly array $users;
    private readonly array $roles;
    private readonly array $genres;
    private readonly array $films;
    /**
     * @var array|string[]
     */
    private readonly array $filmStatuses;

    function __construct()
    {
        $this->roles = [
            'admin' => 'admin',
            'moderator' => 'moderator',
            'user' => 'user',
        ];

        $this->users = [
            [
                'name' => 'Пользователь',
                'email' => 'user@anymail.ru',
                'password' => '123456',
                'role' => 'user',
            ],
            [
                'name' => 'Администратор',
                'email' => 'admin@anymail.ru',
                'password' => '123456',
                'role' => 'admin',
            ],
        ];

        $this->genres = [
            'Crime' => 'Crime',
            'Thriller' => 'Thriller',
            'Adventure' => 'Adventure',
            'Comedy' => 'Comedy',
            'Drama' => 'Drama',
            'Action' => 'Action',
            'Fantasy' => 'Fantasy',
            'Mystery' => 'Mystery',
        ];

        $this->filmStatuses = [
            'pending' => 'pending',
            'on_moderation' => 'on_moderation',
            'ready' => 'ready',
        ];

        $this->films = [
            [
                'id',
                'name' => 'Shutter Island',
                'genres' => 'Drama, Thriller, Mystery',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#977461',
                'released' => '2010',
                'description' => 'In 1954, a U.S. Marshal investigates the disappearance of a murderer, who escaped from a hospital for the criminally insane.',
                'director' => 'Martin Scorsese',
                'starring' => 'Leonardo DiCaprio, Emily Mortimer, Mark Ruffalo',
                'runTime' => 138,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt1130884',
            ],
            [
                'id',
                'name' => 'Once Upon a Time in America',
                'genres' => 'Drama, Thriller, Mystery',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#CBAC79',
                'released' => '1984',
                'description' => 'A former Prohibition-era Jewish gangster returns to the Lower East Side of Manhattan over thirty years later, where he once again must confront the ghosts and regrets of his old life.',
                'director' => 'Sergio Leone',
                'starring' => 'Robert De Niro, James Woods, Elizabeth McGovern',
                'runTime' => 229,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0087843',
            ],
            [
                'id' => 3,
                'name' => 'Moonrise Kingdom',
                'genres' => 'Drama, Thriller, Mystery',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#D8E3E5',
                'released' => '2012',
                'description' => 'A pair of young lovers flee their New England town, which causes a local search party to fan out to find them.',
                'director' => 'Wes Anderson',
                'starring' => 'Jared Gilman, Kara Hayward, Bruce Willis',
                'runTime' => 94,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt1748122',
            ],
            [
                'id' => 4,
                'name' => 'Snatch',
                'genres' => 'Drama, Thriller, Mystery',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#FDFDFC',
                'released' => '2000',
                'description' => 'In 1954, a U.S. Marshal investigates the disappearance of a murderer, who escaped from a hospital for the criminally insane.',
                'director' => 'Guy Ritchie',
                'starring' => 'Jason Statham, Brad Pitt, Benicio Del Toro',
                'runTime' => 104,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0208092',
            ],
            [
                'id' => 5,
                'name' => 'Orlando',
                'genres' => 'Drama',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#D8D3BD',
                'released' => '1992',
                'description' => 'Young nobleman Orlando is commanded by Queen Elizabeth I to stay forever young. Miraculously, he does just that. The film follows him as he moves through several centuries of British history, experiencing a variety of lives and relationships along the way, and even changing sex.',
                'director' => 'Sally Potter',
                'starring' => 'Tilda Swinton, Billy Zane, Quentin Crisp',
                'runTime' => 94,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0107756',
            ],
            [
                'id' => 6,
                'name' => 'We need to talk about Kevin',
                'genres' => 'Drama',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#E1DFDE',
                'released' => '2011',
                'description' => 'Kevin`s mother struggles to love her strange child, despite the increasingly dangerous things he says and does as he grows up. But Kevin is just getting started, and his final act will be beyond anything anyone imagined.',
                'director' => 'Lynne Ramsay',
                'starring' => 'Tilda Swinton, John C. Reilly, Ezra Miller',
                'runTime' => 112,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt1242460',
            ],
            [
                'id' => 7,
                'name' => 'A Star Is Born',
                'genres' => 'Drama',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#C4C0C0',
                'released' => '2018',
                'description' => 'A musician helps a young singer find fame as age and alcoholism send his own career into a downward spiral.',
                'director' => 'Bradley Cooper',
                'starring' => 'Lady Gaga, Bradley Cooper, Sam Elliott',
                'runTime' => 136,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt1517451',
            ],
            [
                'id' => 8,
                'name' => 'Macbeth',
                'genres' => 'Drama',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#F1E9CE',
                'released' => '2015',
                'description' => 'Macbeth, the Thane of Glamis, receives a prophecy from a trio of witches that one day he will become King of Scotland. Consumed by ambition and spurred to action by his wife, Macbeth murders his king and takes the throne for himself.',
                'director' => 'Justin Kurzel',
                'starring' => 'Michael Fassbender, Marion Cotillard, Jack Madigan',
                'runTime' => 113,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt2884018',
            ],
            [
                'id' => 9,
                'name' => 'Bronson',
                'genres' => 'Action',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#FDFDFC',
                'released' => '2008',
                'description' => 'A young man who was sentenced to seven years in prison for robbing a post office ends up spending three decades in solitary confinement. During this time, his own personality is supplanted by his alter-ego, Charles Bronson.',
                'director' => 'Nicolas Winding Refn',
                'starring' => 'Tom Hardy, Kelly Adams, Luing Andrews',
                'runTime' => 92,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt1172570',
            ],
            [
                'id' => 10,
                'name' => 'Matrix',
                'genres' => 'Action',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#B9B27E',
                'released' => '1999',
                'description' => 'A computer hacker learns from mysterious rebels about the true nature of his reality and his role in the war against its controllers.',
                'director' => 'Guy Ritchie',
                'starring' => 'Keanu Reeves, Laurence Fishburne, Carrie-Anne Moss',
                'runTime' => 136,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0133093',
            ],
            [
                'id' => 11,
                'name' => 'What We Do in the Shadows',
                'genres' => 'Comedy',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#A39E81',
                'released' => '2019',
                'description' => 'A look into the daily (or rather, nightly) lives of three vampires who`ve lived together for over 100 years, in Staten Island.',
                'director' => 'Jemaine Clement',
                'starring' => 'Kayvan Novak, Matt Berry, Natasia Demetriou',
                'runTime' => 30,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt3416742',
            ],
            [
                'id' => 12,
                'name' => 'Dardjeeling Limited',
                'genres' => 'Adventure',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#AD9F8B',
                'released' => '2007',
                'description' => 'A year after their father`s funeral, three brothers travel across India by train in an attempt to bond with each other.',
                'director' => 'Wes Anderson',
                'starring' => 'Owen Wilson, Adrien Brody, Jason Schwartzman',
                'runTime' => 91,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0838221',
            ],
            [
                'id' => 13,
                'name' => 'The Revenant',
                'genres' => 'Action',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#92918B',
                'released' => '2015',
                'description' => 'A frontiersman on a fur trading expedition in the 1820s fights for survival after being mauled by a bear and left for dead by members of his own hunting team.',
                'director' => 'Alejandro G. Iñárritu',
                'starring' => 'Leonardo DiCaprio, Tom Hardy, Will Poulter',
                'runTime' => 156,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt1663202',
            ],
            [
                'id' => 14,
                'name' => 'Midnight Special',
                'genres' => 'Action',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#828585',
                'released' => '2016',
                'description' => 'A father and son go on the run, pursued by the government and a cult drawn to the child`s special powers.',
                'director' => 'Jeff Nichols',
                'starring' => 'Michael Shannon, Joel Edgerton, Kirsten Dunst',
                'runTime' => 112,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt2649554',
            ],
            [
                'id' => 15,
                'name' => 'Beach',
                'genres' => 'Adventure',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#EBC996',
                'released' => '2000',
                'description' => 'Vicenarian Richard travels to Thailand and finds himself in possession of a strange map. Rumours state that it leads to a solitary beach paradise, a tropical bliss. Excited and intrigued, he sets out to find it.',
                'director' => 'Danny Boyle',
                'starring' => 'Leonardo DiCaprio, Daniel York, Patcharawan Patarakijjanon',
                'runTime' => 119,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0163978',
            ],
            [
                'id' => 16,
                'name' => 'Johnny English',
                'genres' => 'Comedy',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#F0DBA2',
                'released' => '2003',
                'description' => 'After a sudden attack on the MI5, Johnny English, Britain`s most confident yet unintelligent spy, becomes Britain`s only spy.',
                'director' => 'Peter Howitt',
                'starring' => 'Rowan Atkinson, John Malkovich, Natalie Imbruglia',
                'runTime' => 88,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0274166',
            ],
            [
                'id' => 17,
                'name' => 'Pulp Fiction',
                'genres' => 'Crime',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#795433',
                'released' => '1994',
                'description' => 'The lives of two mob hitmen, a boxer, a gangster & his wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
                'director' => 'Quentin Tarantino',
                'starring' => 'John Travolta, Uma Thurman, Samuel L. Jackson',
                'runTime' => 153,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0110912',
            ],
            [
                'id' => 18,
                'name' => 'No Country for Old Men',
                'genres' => 'Crime',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#BDAD8F',
                'released' => '2007',
                'description' => 'Violence and mayhem ensue after a hunter stumbles upon a drug deal gone wrong and more than two million dollars in cash near the Rio Grande.',
                'director' => 'Ethan Coen',
                'starring' => 'Tommy Lee Jones, Javier Bardem, Josh Brolin',
                'runTime' => 122,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0477348',
            ],
            [
                'id' => 19,
                'name' => 'Fantastic Beasts: The Crimes of Grindelwald',
                'genres' => 'Fantasy',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#B6A99F',
                'released' => '2018',
                'description' => 'In an effort to thwart Grindelwald`s plans of raising pure-blood wizards to rule over all non-magical beings, Albus Dumbledore enlists his former student Newt Scamander, who agrees to help, though he`s unaware of the dangers that lie ahead. Lines are drawn as love and loyalty are tested, even among the truest friends and family, in an increasingly divided wizarding world.',
                'director' => 'David Yates',
                'starring' => 'Eddie Redmayne, Katherine Waterston, Dan Fogler',
                'runTime' => 134,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt4123430',
            ],
            [
                'id' => 20,
                'name' => 'Seven Years in Tibet',
                'genres' => 'Adventure',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#C6CADF',
                'released' => '1997',
                'description' => 'True story of Heinrich Harrer, an Austrian mountain climber who became friends with the Dalai Lama at the time of China`s takeover of Tibet.',
                'director' => 'Jean-Jacques Annaud',
                'starring' => 'Brad Pitt, David Thewlis, BD Wong',
                'runTime' => 136,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0120102',
            ],
            [
                'id' => 21,
                'name' => 'Gangs of new york',
                'genres' => 'Crime',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#A6B7AC',
                'released' => '2002',
                'description' => 'In 1862, Amsterdam Vallon returns to the Five Points area of New York City seeking revenge against Bill the Butcher, his father`s killer.',
                'director' => 'Martin Scorsese',
                'starring' => 'Leonardo DiCaprio, Cameron Diaz, Daniel Day-Lewis',
                'runTime' => 167,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0217505',
            ],
            [
                'id' => 22,
                'name' => 'War of the Worlds',
                'genres' => 'Adventure',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#9B7E61',
                'released' => '2005',
                'description' => 'As Earth is invaded by alien tripod fighting machines, one family fights for survival.',
                'director' => 'Steven Spielberg',
                'starring' => 'Tom Cruise, Dakota Fanning, Tim Robbins',
                'runTime' => 116,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0407304',
            ],
            [
                'id' => 23,
                'name' => 'Legend',
                'genres' => 'Crime',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#E1DAD7',
                'released' => '2015',
                'description' => 'Identical twin gangsters Ronald and Reginald Kray terrorize London during the 1960s.',
                'director' => 'Brian Helgelan',
                'starring' => 'Tom Hardy, Emily Browning, Taron Egerton',
                'runTime' => 132,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt3569230',
            ],
            [
                'id' => 24,
                'name' => 'Aviator',
                'genres' => 'Drama',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#D6CDAF',
                'released' => '2014',
                'description' => 'A biopic depicting the early years of legendary Director and aviator Howard Hughes` career from the late 1920s to the mid 1940s.',
                'director' => 'Martin Scorsese',
                'starring' => 'Leonardo DiCaprio, Cate Blanchett, Kate Beckinsale',
                'runTime' => 170,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt0338751',
            ],
            [
                'id' => 25,
                'name' => 'Bohemian Rhapsody',
                'genres' => 'Drama',
                'posterImage' => 'https://some-link',
                'previewImage' => 'https://some-link',
                'backgroundImage' => 'https://some-link',
                'backgroundColor' => '#929FA5',
                'released' => '2018',
                'description' => 'Bohemian Rhapsody is a foot-stomping celebration of Queen, their music and their extraordinary lead singer Freddie Mercury. Freddie defied stereotypes and shattered convention to become one of the most beloved entertainers on the planet. The film traces the meteoric rise of the band through their iconic songs and revolutionary sound. They reach unparalleled success, but in an unexpected turn Freddie, surrounded by darker influences, shuns Queen in pursuit of his solo career. Having suffered greatly without the collaboration of Queen, Freddie manages to reunite with his bandmates just in time for Live Aid. While bravely facing a recent AIDS diagnosis, Freddie leads the band in one of the greatest performances in the history of rock music. Queen cements a legacy that continues to inspire outsiders, dreamers and music lovers to this day.',
                'director' => 'Bryan Singer',
                'starring' => 'Rami Malek, Lucy Boynton, Gwilym Lee',
                'runTime' => 134,
                'videoLink' => 'https://some-link',
                'previewVideoLink' => 'https://some-link',
                'rating' => 0,
                'scoreCount' => 0,
                'imdb_id' => 'tt1727824',
            ],
        ];

    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedRoles();
        $this->seedUsers();
        $this->seedGenres();
        $this->seedFilmStatuses();

        $filmCount = Film::query()->get()->count();
        if($filmCount <= 0) {
            $this->seedFilms(35);
        }
    }

    private function seedRoles(): void {
        $count = DB::table('roles')->get()->count();

        if($count <= 0) {
            foreach ($this->roles as $role) {
                DB::table('roles')->insert([
                    'id' => Str::uuid(),
                    'name' => $role,
                ]);
            }
        }
    }

    private function seedUsers(): void {
        $count = DB::table('users')->get()->count();

        if($count <= 0) {
            foreach ($this->users as $user) {
                $role = Role::query()->where('name', '=', $user['role'])->first();

                User::query()->create([
                    "id" => Str::uuid(),
                    "name" => $user['name'],
                    "email" => $user['email'],
                    "password" => $user['password'],
                    "role_id" => $role->id,
                ]);
            }
        }
    }

    private function seedGenres(): void {
        $count = Genre::query()->get()->count();

        if($count <= 0) {
            foreach ($this->genres as $genre) {
                Genre::query()->create([
                    'id' => Str::uuid(),
                    'name' => $genre,
                ]);
            }
        }
    }

    private function seedFilmStatuses(): void {
        $count = FilmStatus::query()->get()->count();

        if($count <= 0) {
            foreach ($this->filmStatuses as $filmStatus) {
                FilmStatus::query()->create([
                    'id' => Str::uuid(),
                    'name' => $filmStatus,
                ]);
            }
        }
    }

    private function seedFilms(int $count): void {
        $status = FilmStatus::query()->where('name', '=', 'ready')->first();

        for ($i = 0; $i < $count; $i++) {
            $film = $this->films[array_rand($this->films)];

            $cratedFilm = Film::query()->create([
                'id' => Str::uuid(),
                'name' => $film['name'],
                'poster_image' => $film['posterImage'],
                'preview_image' => $film['previewImage'],
                'background_image' => $film['backgroundImage'],
                'background_color' => $film['backgroundColor'],
                'released' => $film['released'],
                'description' => $film['description'],
                'director' => $film['director'],
                'starring' => $film['starring'],
                'run_time' => $film['runTime'],
                'video_link' => $film['videoLink'],
                'preview_video_link' => $film['previewVideoLink'],
                'rating' => $film['rating'],
                'score_count' => $film['scoreCount'],
                'imdb_id' => $film['imdb_id'],
                'status_id' => $status->id,
            ]);

            $genres = explode(', ', $film['genres']);
            foreach ($genres as $genre) {
                $genre = Genre::query()->where('name', '=', $genre)->first();

                FilmGenre::query()->create([
                    'film_id' => $cratedFilm->id,
                    'genre_id' => $genre->id,
                ]);
            }
        }

//        $count = Film::query()->get()->count();



//        $films = Film::factory(3)
//            ->hasAttached(
//                Genre::query()->whereIn('name', ['Action, '])->get()
//            )
//            ->create();
    }
}
