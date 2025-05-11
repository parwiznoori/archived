@extends('layouts.app')

@section('content')

    <div class="portlet box">
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            <div class="panel-body">        
                <div class="col-md-4">
                    <form method="post" action="archivesearch">
                        @csrf
                        <div class="input-group">
                            <input type="text" id="searchbox" required name="search" placeholder="جستجو..." class="form-control">
                            <span class="input-group-btn"> 
                                <button type="submit" id="searchbutton" class="btn btn-primary">جستجو</button>
                            </span>
                        </div>
                    </form>
                </div>
                
                    <button type="submit" id="printButton"  class="btn btn-primary">چاپ اطلاعات</button>
                    <button id="resetButton" class="btn btn-danger">تازه سازی</button>
            </div>      
        </div>
        
       
      
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">ایدی کانکور</th>
                        <th scope="col">نام محصل</th>
                        <th scope="col">پوهنتون </th>
                        <th scope="col">پوهنحی </th>
                        <th scope="col">دیپارتمنت </th>
                       
                        
                    </tr>
                </thead>
                <tbody id="buddyrecord">
                    @foreach ($archivesearchbar as $search)
                    <tr>
                        <td>{{ $search->kankor_id }}</td>
                        <td>{{ $search->name }}</td>
                        <td>{{ $search->un_name }}</td>
                        <td>{{ $search->fa_name }}</td>
                        <td>{{ $search->de_name }}</td>
                       
                    </tr>
                    <tr>
                        <td colspan="5">
                            <img style="max-width: 100%!important; max-height:550px;" class="img-fluid " src='{{ $search->path }}'/>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
       
    
    </div>

  <style>
/* Style for the circular button */
/* Style for the table */
.table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
}

/* Style for table head */
thead {
    background-color: #395d81; /* Dark color */
    color: #fff; /* White text color */
}

/* Style for table head cells */
th {
    padding: 12px 15px;
    text-align: center;
    font-weight: bold;
}

/* Style for table body */
tbody {
    background-color: #f8f9fa; /* Light color */
}

/* Style for table body cells */
td {
    padding: 10px 15px;
    text-align: center;
}

/* Style for alternate rows */
tbody tr:nth-child(even) {
    background-color: #e9ecef; /* Lighter color */
}


/* Style for hiding the button when printing */
@media print {
    #printButton {
        display: none;
    }

    #searchbox {
        display: none;
    }

    #searchbutton{
        display: none;
    }
    #resetButton{
        display: none;
    }
}


</style>



<!-- Circular button to trigger printing -->


<script>
// Function to handle the click event on the print button
function handlePrintButtonClick() {
    window.print(); // This triggers the browser's print functionality
    document.getElementById('printButton') // Hide the button after it's clicked
}

// Add event listener to the print button
document.getElementById('printButton').addEventListener('click', handlePrintButtonClick);
document.getElementById('resetButton').addEventListener('click', function() {
        // Clear the search input field
        document.getElementById('searchbox').value = '';
        
        document.getElementById('buddyrecord').innerHTML  = '';
        
    });
</script>

@endsection('content')

