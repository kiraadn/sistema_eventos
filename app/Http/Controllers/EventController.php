<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;

class EventController extends Controller
{


    public function index()
    {
        $search = request('search');

        if($search){

            $events = Event::where([
                ['title', 'like', '%'.$search.'%']
            ])->get();

        } else {
            $events = Event::all();
        }



        return view('welcome',['events' => $events, 'search' => $search]);
    }


    public function create()
    {
        return view('events.create');
    }


    public function store(Request $request)
    {
        $event = new Event;

        $event->title = $request->title;
        $event->date = $request->date;
        $event->city = $request->city;
        $event->private = $request->private;
        $event->description = $request->description;
        $event->items = $request->items;

        // Image Upload
        // Verifica se é o Arquivo que queremos
        if($request->hasFile('image') && $request->file('image')->isValid()){

            // Processamento dos dados...
            $requestImage = $request->image;

            $extension = $requestImage->extension();

            // Faz uma hash e deixa o nome do arquivo único...
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

            // Adiciona a imagem na pasta do server: Faz o upload
            $requestImage->move(public_path('img/events'), $imageName);

            //Adicionamos a imagem a propriedade:Image do objecto
            $event->image = $imageName;
        }

        $user = auth()->user();
        $event->user_id = $user->id;

        $event->save();

        return redirect('/')->with('msg', 'Evento criado com sucesso!');
    }


    public function show($id) {

      $event = Event::findOrFail($id);

      $eventOwner = User::where('id', $event->user_id)->first()->toArray();

      return view('events.show', ['event' => $event, 'eventOwner' => $eventOwner]);
    }


    public function dashboard() {

        $user = auth()->user();

        $events = $user->events;
        $eventsAsParticipant = $user->eventsAsParticipant;

        return view('events.dashboard', ['events'=> $events, 'eventsAsParticipant' => $eventsAsParticipant]);
    }

    public function destroy($id) {

        Event::findOrFail($id)->delete();

        return redirect('/dashboard')->with('msg', 'Evento excluido com sucesso!');
    }


    public function edit($id){

        $user = auth()->user();

        $event = Event::findOrFail($id);

        if($user->id != $event->user_id) {
            return redirect('/dashboard')->with('msg', 'O evento não é de sua propriedade!');
        }

        return view('events.edit', ['event' => $event]);
    }


    public function update(Request $request){

        $data = $request->all();

        // Image Upload
        // Verifica se é o Arquivo que queremos
        if($request->hasFile('image') && $request->file('image')->isValid()){

            // Processamento dos dados...
            $requestImage = $request->image;

            $extension = $requestImage->extension();

            // Faz uma hash e deixa o nome do arquivo único...
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

            // Adiciona a imagem na pasta do server: Faz o upload
            $requestImage->move(public_path('img/events'), $imageName);

            //Adicionamos a imagem a propriedade:Image do objecto
            $data['image'] = $imageName;
        }

       Event::findOrFail($request->id)->update($data);

       return redirect('/dashboard')->with('msg', 'Evento editado com sucesso!');


    }


    public function joinEvent($id) {

        $user = auth()->user();

        $user->eventsAsParticipant()->attach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Sua presença está confirmada no evento ' . $event->title);
    }


}
