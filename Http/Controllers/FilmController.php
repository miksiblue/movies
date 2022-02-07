<?php

namespace App\Http\Controllers;


use App\Models\Director;
use App\Models\Film;
use App\Models\Comment;
use App\Models\Genre;
use App\Models\Glumac;
use App\Models\User;
use Illuminate\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Finder\Iterator\SortableIterator;
use Illuminate\Database\Eloquent\Builder;
use function PHPUnit\Framework\isNull;

class FilmController extends Controller
{


    public function index(Film $film)
    {
        $users = User::all();

        $filmovi1 = Film::all()->random(1);
        $filmovi = Film::query()->paginate(12);
        $name = Film::orderBy('naziv', 'ASC')->get();



        $averageFilmScoreJoin=DB::table('films')
            ->join('film_user' , 'films.id' ,'=' , 'film_user.film_id')
            ->select('films.naziv','films.opis','film_id', \DB::raw('avg(score) as avg'))
            ->groupBy('films.naziv','film_id','films.opis')
            ->orderByDesc('score')
            ->get();





//        $users = DB::table('users')
//            ->join('contacts', 'users.id', '=', 'contacts.user_id')
//            ->join('orders', 'users.id', '=', 'orders.user_id')
//            ->select('users.*', 'contacts.phone', 'orders.price')
//            ->get();



        return view('film/index', compact('filmovi', "filmovi1", 'film', 'name', 'users', 'averageFilmScoreJoin'));
    }

    public function create(Film $film)
    {
        $genres = Genre::all();
        $glumci = Glumac::all();
        $filmovi = Film::all();
        return view('film/create', compact('filmovi', 'film', 'glumci', 'genres'));
    }

    public function store(Request $request, Film $film, Glumac $glumac, Genre $genre)
    {


        $film = Film::create($this->validateRequests());
        $input['glumac'] = $request->input('glumac');
        $film->glumci()->attach($input['glumac']);
        $input['genre'] = $request->input('genre');
        $film->genres()->attach($input['genre']);
        $this->storeImage($film);
        return redirect('film/index');
    }

    public function store2(Request $request, Film $film, User $user)
    {
        $a = auth()->user()->id;

        $input['score'] = $request->input('score');
        $film->users()->syncWithoutDetaching([
            $a => ['score' => $input['score']]
        ]);
        return back();
    }

    public function show(Film $film, User $user)
    {
        $users = User::all();
        $filmovi = Film::all();

        $ocena=DB::table('film_user')->select('score')->where('film_id', '=', $film->id)->get();
        $sum = DB::table('film_user')->select('score')->where('film_id', '=', $film->id)->sum('score');
        $allUsersIds = DB::table('film_user')->select('user_id')->where('film_id', '=', $film->id)->count('user_id');

        if (is_null($ocena) )
            $ocena = 1;
        else
            $ocena = $sum / $allUsersIds;





        return view('film/show', compact('film', 'filmovi', 'user', 'users', 'ocena'));
    }

    public function edit(Film $film)
    {
        $genres = Genre::all();
        $glumci = Glumac::all();
        $filmovi = Film::all();
        return view('film/edit', compact('film', 'filmovi', 'glumci', 'genres'));
    }

    public function update(Film $film, Request $request)
    {

        $data = \request()->validate([
            'naziv' => 'required',
            'opis' => 'required',
            'release_date' => 'required',
            'slika' => 'sometimes|file|image|max:5000',
        ]);
        $input['glumac'] = $request->input('glumac');
        $film->glumci()->syncWithoutDetaching($input['glumac']);
        $input['genre'] = $request->input('genre');
        $film->genres()->syncWithoutDetaching($input['genre']);

        $this->storeImage($film);
        $film->update($data);
        return back();

    }


    public function destroy(Film $film)
    {

        $film->delete();
        return redirect('film/index');

    }

    public function validateRequests()
    {
        return \request()->validate([
            'naziv' => 'required',
            'opis' => 'required',
            'slika' => 'sometimes|file|image|max:5000',
            'release_date' => 'required'
        ]);
    }

    public function storeImage($film)
    {
        if (\request()->has('slika')) {
            $film->update([
                'slika' => \request()->slika->store('upolads', 'public'),
            ]);
            $image = Image::make(public_path('storage/' . $film->slika))->fit(3000, 30, null, 'top-left');
            $image->save();
        }
    }
}
