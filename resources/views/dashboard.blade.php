@extends('dashboard.app', ['title' => 'Dashboard'])

@section('body')
    <div>
        {!! $dataTable->table() !!}
        <div class="relative-bottom row">
            <audio id="player" controls="controls">
                <source id="source" src="" type="audio/mpeg"/>
            </audio>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}

    <script>

        let hostname = "{{ url('') }}";
        let table = $('#music-table');

        /***************************************************
         *
         * Toast setup...
         *
         ***************************************************/
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            didOpen: (toast) => {
                table.DataTable().ajax.reload();
            }
        })

        $.fn.playSong = function () {
            let url = $(this).data('file-path');
            let player = $('#player');
            $("#source").attr("src", 'songs/' + url);

            player[0].pause();
            player[0].load();
            player[0].oncanplaythrough = player[0].play();
        }

        $.fn.addToPlaylist = function () {
            let musicId = $(this).data('id');

            Swal.fire({
                title: 'Please Wait!',
                showCancelButton: false,
                showConfirmButton: false,
                allowEscapeKey: false,
                allowOutsideClick: false,
                button: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                },
            });

            let payload = {
                _token: "{{ csrf_token() }}",
            }

            $.ajax({
                url: hostname + '/getPlaylists',
                type: 'Post',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: function (response) {
                    Swal.close();
                    let data = JSON.stringify(response);
                    let playlists = $.parseJSON(data);

                    let html = '<select class="form-control" id="selectPlaylist"> <option selected>Choose Playlist</option> ';
                    playlists.forEach(function (playlist) {
                        html += '<option id="' + playlist.id + '">' + playlist.name + '</option>'
                    })
                    html += '</select>';

                    Swal.fire({
                        title: 'Add To Playlist',
                        html: html,

                        preConfirm: () => {

                            let playlistsId = $('#selectPlaylist').find(':selected').attr('id');

                            if (playlistsId) {

                                let data = {
                                    _token: "{{ csrf_token() }}",
                                    musicId: musicId,
                                    playlistId: playlistsId,
                                }
                                
                                return $.ajax({
                                    url: hostname,
                                    type: 'Post',
                                    dataType: 'json',
                                    contentType: 'application/json',
                                    data: JSON.stringify(data),

                                    success: function (response) {
                                        let data = JSON.stringify(response);
                                        let status = $.parseJSON(data);
                                        Toast.fire({
                                            icon: status.type,
                                            title: status.message,
                                        });
                                    },
                                    error: function (response) {
                                        let data = JSON.stringify(response);
                                        let responseText = $.parseJSON(data);
                                        let status = $.parseJSON(responseText.responseText);
                                        Toast.fire({
                                            icon: 'error',
                                            title: status.message,
                                        });
                                    }
                                });
                            } else {
                                Swal.showValidationMessage('Please Select Playlist');

                            }
                        },
                        allowOutsideClick: false,
                        showCancelButton: true,
                        confirmButtonText: 'Add',
                        confirmButtonColor: '#009efb',
                        cancelButtonColor: '#f62d51',
                        showLoaderOnConfirm: true
                    });

                },
                error: function () {
                    Toast.fire({
                        icon: 'error',
                        title: 'Error occurred while fetching data'
                    });
                }
            })
        }

    $.fn.removeFromPlaylist = function(){
        return console.log('In remove function');
    }

    </script>
@endpush
