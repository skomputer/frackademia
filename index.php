<html>
<head>
<title>Frackademia in Depth</title>
<script type="text/javascript" src="//code.jquery.com/jquery-2.1.3.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.11.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.7.0/underscore-min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/frackademia.css">
</head>
<body>
<h1>Frackademia in Depth</h1>
<div id="datatable-filters" class="form-inline">
</div>
<table id="datatable" class="table table-striped table-bordered">
  <thead>
    <tr>
      <th></th>
      <th>Title</th>
      <th>Issuing Organization</th>
      <th>Topic</th>
      <th>Reviewed</th>
      <th>Strength</th>
      <th>Type</th>
    </tr>
  </thead>
</table>
<script>
var fieldMap = {
  'title': 'Title',
  'date': 'Date',
  'org': 'Issuing Org',
  'org_type': 'Type of Organization Issuing',
  'topic': 'Topic',
  'peer_reviewed': 'Peer reviewed',
  'ties_strength': 'Strength of Industry Ties',
  'ties_type': 'Type of Industry Ties',
  'duplicate': 'Duplicate?',
  'ties_notes': 'Notes on Industry Ties',
  'other_notes': 'Other Notes',
  'authors': 'Authors',
  'orgs': 'Organizations',
  'link': 'Link'
};

var details_table = function(d) {
  if (!d.authors && !d.ties_notes && !d.other_notes) {
    return "<strong>No details.</strong>";
  }

  var str = '<table class="details-table" border="0" style="padding-left: 50px;">';
  if (d.authors) {
    str += '<tr>'+
      '<td>Authors</td>'+
      '<td>'+d.authors+'</td>'+
    '</tr>';
  }
  if (d.ties_notes) {
    str += '<tr>'+
      '<td>Notes on Ties</td>'+
      '<td>'+d.ties_notes+'</td>'+
    '</tr>';
  }
  if (d.other_notes) {
    str += '<tr>'+
      '<td>Other Notes</td>'+
      '<td>'+d.other_notes+'</td>'+
    '</tr>';
  }
  str += '</table>';
  return str;
};

var data = <?php echo file_get_contents('data/frackademia-data.json'); ?>;
$(document).ready(function() {
  var tbl = $('#datatable').DataTable({
    data: data,
    dom: "<'buttons'>ifrt",
    pageLength: 200,
    order: [[1, 'asc']],
    language: {
      search: "",
      searchPlaceholder: "search"
    },
    columns: [
     {
        width: '1%',
        data: null,
        className: 'details-control',
        orderable: false,
        defaultContent: ''
      },
      {
        data: 'title',
        name: 'title',
        width: "25%",
        render: function(data, type, row) {
          var title = data.slice(1, data.length-1);
          if (row.link) {
            var a = document.createElement('a');
            a.href = row.link;
            a.setAttribute('class', 'entity-link');
            a.innerHTML = title;
            return a.outerHTML;
          } else {
            return title;
          }
        }
      },
      {
        data: 'org',
        name: 'org',
        width: "20%"
      },
      {
        data: 'topic',
        name: 'topic',
        width: "10%"
      },
      {
        data: 'peer_reviewed',
        name: 'peer_reviewed',
        width: "4%"
      },
      {
        data: 'ties_strength',
        name: 'ties_strength',
        width: "5%"
      },
      {
        data: 'ties_type',
        name: 'ties_type',
        width: "2%"
      },
      {
        data: 'authors',
        name: 'authors',
        visible: false
      },
      {
        data: 'ties_notes',
        name: 'ties_notes',
        visible: false
      },
      {
        data: 'other_notes',
        name: 'other_notes',
        visible: false
      }
    ]
  });

  // Add event listener for opening and closing details
  $('#datatable tbody').on('click', 'td.details-control', function() {
    var tr = $(this).closest('tr');
    var row = tbl.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    }
    else {
      // Open this row
      row.child(details_table(row.data())).show();
      tr.addClass('shown');
    }
  });

  var form = $("#datatable-filters")[0];
  var filter_fields = ['ties_strength', 'ties_type', 'peer_reviewed', 'topic'];
  filter_fields.forEach(function(field) {
    var values = [];
    data.forEach(function(study) {
      values.push(study[field]);
    });
    var select = document.createElement('select');
    select.setAttribute('class', 'form-control');
    var sorted = _.uniq(_.compact(values)).sort();
    sorted.unshift(fieldMap[field]);
    sorted.forEach(function(value, index) {
      var option = document.createElement("option");
      option.innerHTML = value;
      option.value = index == 0 ? "" : value;
      select.appendChild(option);
    });
    $(select).on("change", function() {
      tbl.columns(field + ":name").search($(this).val()).draw();
    });
    form.appendChild(select);
  });

  $("#datatable").css('width', '100%');
  $("#datatable_filter input").removeClass("input-sm");
});
</script>
</body>
</html>