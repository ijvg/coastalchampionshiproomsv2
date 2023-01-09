@extends('voyager::master')

{{-- @section('content') --}}

@section('page_header')
    <div class="container-fluid">
      <h1 class="page-title">Tournaments</h1>

      <a href="{{ route('voyager.tournaments.create') }}" class="btn btn-success btn-add-new">
        <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
      </a>
@stop

@section('content')


<div class="page-content browse container-fluid">

  <div class="row">
    <div class="col-md-12">
        <div class="panel panel-bordered">
            <div class="panel-body">
              <div class="table-responsive">
                <table id="tournamentDataTable" class="table table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                          <th>Name</th>
                          <th>Start Date</th>
                          <th class="actions text-right dt-not-orderable">Actions</th>
                        </tr>
                    </thead>
                </table>
              </div>
            </div>
        </div>
    </div>
  </div>
</div>

 {{-- <!--<div class="table-responsive">-->
    <table id="tournamentsTable" style="width: 100%;" class="ui celled table">
      <thead>
        <tr>
          <!--<td>Id</td>-->
          <th>Name</th>
          <th>Start Date</th>
          <th>Action</th>
          <!--<td>End Date</td>-->
        </tr>
      </thead>
    </table>
    <!--<table id="dataTable" class="table table-hover">
        <thead>
            <tr>
              <th>

              </th>
            </tr>
        </thead>
    </table>-->
  <!--</div>--> --}}


{{-- <div class="container-fluid tournamentManagerWrap bodyWrap">
    <div class="site-body padding-top">

      <div class="tournamentsCon container-fluid">

        <div class="grid tournamentSelectGrid border" style="width: 100%;">
          <div class="row stretch-row no-gutters">

            @foreach ($tournaments as $tournament)

              <div class="col-md-6 col-xs-12 col-xlg-4 stretch-col tournamentCol ">
                <h2 class="contest-col-header">{{ $tournament->name }}</h2>
                  <!--<div class="tournamentCard card">
                      <h2 class="contest-col-header">{{ $tournament->name }}</h2>

                      <a href="/{{ Request::path() }}/tournament/{{ $tournament->slug }}" class="text-warning stretched-link"></a>
                  </div>-->
              </div>

            @endforeach

          </div>
        </div>
        
    </div>
  
  </div>

</div>--}}

@stop

@section('css')

  {{-- <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}"> --}}

  <!-- Datatables CSS CDN -->
 <!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">-->
 <!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">-->

 <!--<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.css">
 <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.semanticui.min.css">-->

@stop

@section('javascript')



  {{-- <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>--}}

  <!-- Datatables JS CDN -->
  <!--<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>-->

  <!-- <script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script> -->

  <!--<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.semanticui.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.js"></script>-->

  <script type="text/javascript">
  window.onload = function() {
    $(document).ready(function(){

      
      // DataTable
      //$('#tournamentsTable').DataTable({
      $('#tournamentDataTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "/admin/tournament-manager/get-tournaments",
        columns: [
            //{ data: 'id' },
            { data: 'name',
              render: function (data, type, row, meta) {
                return '<div>'+data+'</div>';
              } },
            { data: 'start_date' },
            {
              "data": {id: 'id', slug: 'slug'},
              "render": function (data, type, row, meta) {
                return '<a class="btn btn-sm btn-primary pull-right edit" id='+data.id+' href="/admin/tournament-manager/tournament/'+data.slug+'"><i class="voyager-edit" style="margin-right: 3px;"></i><span class="hidden-xs hidden-sm">Edit</span></a>';
              },
              "sClass": "bread-actions no-sort no-click"
            }
        ],
        columnDefs: [
          {'targets': 'dt-not-orderable', 'searchable': false, 'orderable': false},
        ]
      });

    });
  };
</script>
@stop

