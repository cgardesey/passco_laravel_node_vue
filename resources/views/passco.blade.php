@extends('layouts.app')

@section('content')

    <div class="container" >
        <div >
            <timer user_id="{{ auth()->user()->id }}"></timer>
        </div>

        <subscription-modal
                token="{{ csrf_token() }}"
                name="{{ auth()->user()->name }}"
                :user_id="{{ auth()->user()->id }}"
                :timeremaining="{{ auth()->user()->timeremaining }}">

        </subscription-modal>
        <div class="dropdown" >
            <button id="dLabel" role="button" data-toggle="dropdown" class="btn btn-outline-primary">
                {{--<span>@{{ selectedPath }}</span>--}}
                {{--<span class="caret"></span>--}}
                @{{ selectedPath }}
            </button>
            <multi-level-dropdown
                    :nodes="nodes">
            </multi-level-dropdown>
        </div>
        <hr/>
        <past-questions></past-questions>
        <spinner></spinner>
    </div>
@endsection
