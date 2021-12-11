<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');

            body{
                background-color: #222;
                color: #adadad !important;
                padding: 25px;
                font-family: 'Roboto', sans-serif !important;
            }

            h1{
                color: #f7f7f7;
            }

            #container{
                margin-top: 50px;
            }
        </style>

        <title>Sutherlands | Aaron Balentine</title>
    </head>
    <body>
        <div id="container" class="row">
            <div class="col-xs-12 col-md-6 offset-md-3 text-center">
                <h1>{{ env('APP_NAME') }}</h1>
                <h2>Aaron Balentine</h2>
            </div>
        </div>  
        <div id="container" class="row">
            <div id="searchWrapper" class="col-xs-12 col-md-6 offset-md-3">
                <label for="search">Find your next movie:</label>
                <input id="search" type="text" class="form-control" placeholder="Enter a movie title...">
                <div id="alertWrapper" style="display: none; margin-top: 5px;" class="col-xs-12">
                    <div id="alert" class="alert alert-danger" role="alert"></div>
                </div>
                <br>
                <button id="submitSearch" class="btn btn-info form-control">Search</button>
            </div>
        </div>
        <div class="row">
            
        </div>
        <div id="resultWrapper" style="display:none; margin-top: 20px;" class="row">
            <div class="col-xs-12 col-md-8 offset-md-2">
                <div class="row">
                    <div class="col-xs-0 col-md-3">
                        <h3 id="movieTitle"></h3>
                        <img id="moviePoster" style="max-width: 100%;">
                        <br>
                        <span id="movieRuntime"></span>
                        <br>
                        <small id="movieYear"></small>
                    </div>
                    <div class="col-xs-12 col-md-9">
                        <label for="movieOverview"><b><u>Description:</u></b></label>
                        <p id="movieOverview"></p>
                        <br>
                        <label for="movieCastList"><b><u>Cast:</u></b></label>
                        <ul id="movieCastList"></ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>

<script>

//Convert time in minutes to time in hours and minutes
function convert_minutes(mins){

    //divide minutes by 60 to get hours, use the remainder as minutes
    var hours = Math.floor(mins / 60);          
    var minutes = mins % 60;
    var runtime = '';

    //Format Output
    if(hours > 0){
        runtime = hours + 'h ';
    }

    if(minutes > 0){
        runtime += minutes + 'm';
    }

    return runtime;

}

</script>

<script>
    $('#submitSearch').on('click', function() {

        let query = $('#search').val();
        
        $.post("/api/tmdb/search",
        {
            query: query
        },
        function(data, status){

            //If there is no error, set and display output
            if(data['status'] != 'error'){

                //Make sure results wrapper is hidden (for multiple requests)
                $('#resultWrapper').hide();

                $('#movieCastList').empty();

                let runtime = convert_minutes(data['details'].runtime);
                
                $('#movieTitle').text(data['details'].title);
                $('#moviePoster').attr('src', 'https://image.tmdb.org/t/p/original/' + data['details'].poster_path);
                $('#movieYear').text(data['details'].formatted_date);
                $('#movieRuntime').text(runtime);
                $('#movieOverview').text(data['details'].overview);

                for(var i=0; i<10; i++){

                    var member = '<li>' 
                                    + data['credits'].cast[i].character
                                    + ' - ' + data['credits'].cast[i].name
                                + '</li>';

                    $('#movieCastList').append(member);
                }

                $('#resultWrapper').show();
            } //Else set alert text and show it for a short duration
            else{
                $('#alert').text(data.message);

                $("#alertWrapper").fadeTo(2000, 500).slideUp(500, function(){
                        $("#alertWrapper").slideUp(500);
                });
            }

        });

    });
</script>