@extends('layouts.app')

@section('css')
    @vite('css/index.css')
@endsection

@section('content')
    <div class="todo__alert">
        <div class="todo__alert--success">
            Todoを作成しました
        </div>
    </div>

    <div class="todo__content">
        <form class="create-form" method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <div class="create-form__item">
                <input class="create-form__item-input" type="text" name="content" value="{{ old('content') }}">
            </div>
            <div class="create-form__button">
                <button class="create-form__button-submit" type="submit">作成</button>
            </div>
        </form>
        <div class="todo-table">
            <table class="todo-table__inner">
                <tr class="todo-table__row">
                    <th class="todo-table__header">Todo</th>
                </tr>
                @foreach ($tasks as $task)
                    <tr class="todo-table__row">
                        <td class="todo-table__item">
                            <form class="update-form" method="POST" action="{{ route('tasks.update', $task) }}">
                                @csrf
                                @method('PUT') {{-- PUTメソッドの擬似フォーム送信が必要な場合 --}}
                                <div class="update-form__item">
                                    <input class="update-form__item-input" type="text" name="content"
                                        value="{{ $task->content }}">
                                </div>
                                <div class="update-form__button">
                                    <button class="update-form__button-submit" type="submit">更新</button>
                                </div>
                            </form>
                        </td>
                        <td class="todo-table__item">
                            <form class="delete-form" method="POST" action="{{ route('tasks.destroy', $task) }}">
                                @csrf
                                @method('DELETE') {{-- DELETEメソッドの擬似フォーム送信が必要な場合 --}}
                                <div class="delete-form__button">
                                    <button class="delete-form__button-submit" type="submit">削除</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection