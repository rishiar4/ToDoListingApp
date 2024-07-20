@extends('layouts.app')

@section('title')
    My Todo App
@endsection
@section('content')

    <div class="row mt-3">
        <div class="col-12 align-self-center">
            <input type="text" name="" id="">
            <a href="/"><span class="btn btn-primary">Add Task</span></a>
        </div>
    </div>
    <div class="row mt-3 mr-6">
        <div class="col-12 align-self-center">
            <ul class="list-group">
                @foreach($todos as $todo)
                    <li class="list-group-item"><a href="details" style="color: cornflowerblue">{{$todo->name}}</a></li>
                @endforeach
            </ul>
        </div>
    </div>

@endsection