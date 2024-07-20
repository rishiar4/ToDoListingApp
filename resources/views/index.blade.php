@extends('layouts.app')

@section('title')
    My Todo App
@endsection

@section('content')
    <div class="row mt-3">
        <div class="col-12 d-flex justify-content-center">
            <span class="btn btn-secondary show-all  ms-2">Show All</span>

        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 d-flex justify-content-center">
            <input type="text" name="task" id="task-input">
            <span class="btn btn-primary submit ms-2">Add Task</span>

        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        </div>
    </div>

    <div class="row mt-3 mr-6">
        <div class="col-12 align-self-center">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Task</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="task-list">
                    @foreach ($todos as $todo)
                        <tr data-id="{{ $todo->id }}">
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $todo->name }}</td>
                            <td>{{ $todo->completed ? 'Completed' : 'Pending' }}</td>
                            <td>
                                @if (!$todo->completed)
                                    <input type="checkbox" class="complete-task">
                                @endif
                                <span class="delete-task ms-2" style="cursor: pointer;" data-id="{{ $todo->id }}">
                                    <i class="fas fa-trash-alt"></i>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this task?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            function updateCounters() {
                $('#task-list tr').each(function(index, row) {
                    $(row).find('th').text(index + 1);
                });
            }

            let taskCounter = {{ $todos->count() }};

            $('.submit').on('click', function() {
                let task = $('#task-input').val();

                $.ajax({
                    url: '/todos',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: task
                    },
                    success: function(response) {
                        $('#task-input').val('');

                        taskCounter++;
                        let newTask = `<tr data-id="${response.id}">
                            <th scope="row">${taskCounter}</th>
                            <td>${response.name}</td>
                            <td>${response.completed ? 'Completed' : 'Pending'}</td>
                            <td>
                                <input type="checkbox" class="complete-task">
                                <span class="delete-task ms-2" style="cursor: pointer;" data-id="${response.id}">
                                    <i class="fas fa-trash-alt"></i>
                                </span>
                            </td>
                        </tr>`;
                        $('#task-list').append(newTask);

                        updateCounters();
                    },
                    error: function(response) {
                        $('#error-message').text("The name has already been taken.")
                            .removeClass('d-none');

                        setTimeout(function() {
                            $('#error-message').addClass('d-none');
                        }, 3000); // 3 seconds
                    }
                });
            });

            $('#task-list').on('click', '.complete-task', function() {
                let taskRow = $(this).closest('tr');
                let taskId = taskRow.data('id');

                $.ajax({
                    url: '/todos/' + taskId,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        completed: true,
                        id: taskId
                    },
                    success: function(response) {
                        taskRow.remove();
                        updateCounters();
                    },
                    error: function(response) {
                        $('#error-message').text(
                                "An error occurred while marking the task as completed.")
                            .removeClass('d-none');

                        setTimeout(function() {
                            $('#error-message').addClass('d-none');
                        }, 3000); // 3 seconds
                    }
                });
            });

            let deleteTaskId;

            $('#task-list').on('click', '.delete-task', function() {
                deleteTaskId = $(this).data('id');
                $('#deleteModal').modal('show');
            });

            $('#confirm-delete').on('click', function() {
                $.ajax({
                    url: '/todos/' + deleteTaskId,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('tr[data-id="' + deleteTaskId + '"]').remove();
                        updateCounters();
                        $('#deleteModal').modal('hide');
                    },
                    error: function(response) {
                        $('#error-message').text("An error occurred while deleting the task.")
                            .removeClass('d-none');

                        setTimeout(function() {
                            $('#error-message').addClass('d-none');
                        }, 3000);
                    }
                });
            });

            $('.show-all').on('click', function() {
                $.ajax({
                    url: '/todos',
                    method: 'GET',
                    data: {
                        show_all: true
                    },
                    success: function(response) {
                        console.log(response);
                        $('#task-list').empty();
                        taskCounter = response.length;

                        $.each(response, function(index, todo) {
                            let newTask = `<tr data-id="${todo.id}">
                                <th scope="row">${index + 1}</th>
                                <td>${todo.name}</td>
                                <td>${todo.completed ? 'Completed' : 'Pending'}</td>
                                <td>
                                    ${!todo.completed ? '<input type="checkbox" class="complete-task">' : ''}
                                    <span class="delete-task ms-2" style="cursor: pointer;" data-id="${todo.id}">
                                        <i class="fas fa-trash-alt"></i>
                                    </span>
                                </td>
                            </tr>`;
                            $('#task-list').append(newTask);
                        });
                    },
                    error: function(response) {
                        $('#error-message').text("An error occurred while fetching tasks.")
                            .removeClass('d-none');

                        setTimeout(function() {
                            $('#error-message').addClass('d-none');
                        }, 3000); // 3 seconds
                    }
                });
            });
        });
    </script>
@endsection
