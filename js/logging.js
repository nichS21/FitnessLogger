/*
*   These functions created by Nick for the logging page.
*/

//Function that allows user to dynamically add a row to the log table
function addExercise(){
    let table = document.getElementById('logTable');
    let newRow = table.insertRow();                     //insert row at end of table
    
    //set ID of new row accordingly - added directly at end of table
    let ID = countRows();
    newRow.id = "row" + ID;

    newRow.innerHTML = "<td>Added Exercise</td>" + 
                       "<td>4</td>" + 
                       "<td>5</td>" + 
                       "<td>405</td>" +
                       "<td>N/A</td>" + 
                       "<td>New PR</td>" + 
                       "<td class=\"modifyTD\"> <i class=\"bi bi-floppy saveBtn clickable\"></i>  </td>" +
                       "<td class=\"modifyTD\"> <i class=\"bi bi-trash delBtn clickable\" onclick=\"openModal('" + ID + "')\"></i>  </td>";
}


//Function that deletes a row out of a the log table
function deleteExercise(rowID){
    let table = document.getElementById('logTable');
    table.deleteRow(rowID); 

    renameRows(table);                        //IDs for each row may have to be recalculated

    closeModal();                             //Close modal on the way out
}

//Function to loop over rows in table and reassign their IDs, given possibility that 'addExercise' may add a row that has an ID of a row that was previously deleted
function renameRows(table)
{
    let rowCount = table.rows.length;

    //for each row in the table (not including the header row)
    for(let i = 1; i < rowCount; i++)
    {
        let currRow = table.rows[i];
        currRow.id = "row" + i;

        //edit delete button of this row to have proper new, ID
        currRow.cells[7].innerHTML = "<i class=\"bi bi-trash delBtn clickable\" onclick=\"openModal('" + i + "')\">";
    }
}


//This function to count rows on page for generating IDs for dynamically added rows
function countRows(){
    let table = document.getElementById('logTable');
    let rows = table.rows.length;            //Count all rows within the table's body  (Note: If have 3 rows, will return header row and one invisible row at the end of the table)   
    rows--;                                             //(table rows + header row + invisible row) - 1 = new row ID
    return rows;
}

//function to open delete confirmation modal on screen
function openModal(rowID)
{
    let modal = document.getElementById('delModal');
    modal.style.display = "block";                        //show  modal 

    //add event listeners to the buttons in the modal
    document.getElementById('modalCon').addEventListener("click", function(){ deleteExercise(rowID) }, { once: true });  //add listener, and automatically remove it after one event fires
    document.getElementById('modalCan').addEventListener("click", closeModal, { once: true });  
}

//function to hide modal after canceling a delete
function closeModal()
{
    let modal = document.getElementById('delModal');
    modal.style.display = "none";                        //hide  modal 

    //remove listeners placed on the buttons (don't want delete event for a row we cancelled earlier to go off later)
    //Delete button
    var oldDelete = document.getElementById("modalCon");
    var newDelete = oldDelete.cloneNode(true);
    oldDelete.parentNode.replaceChild(newDelete, oldDelete);

    oldDelete.remove();

    //Cancel button
    var oldCancel = document.getElementById("modalCan");
    var newCancel = oldCancel.cloneNode(true);
    oldCancel.parentNode.replaceChild(newCancel, oldCancel);

    oldCancel.remove();
}

function rowListeners()
{
    let table = document.getElementById('logTable');
    let rowCount = table.rows.length;

    for(i = 1; i < rowCount; i++)
    {
        currRow = table.rows[i];
        currRow.addEventListener('input', function(){ showUnsaved(currRow.id) });       //whenever any input field modified swap to save button
    }
}

//Function that changes default edit button to save btn for a modified row
function showUnsaved(ID)
{
    let row = document.getElementById(ID);

    row.cells[6].innerHTML = "<i class=\"bi bi-floppy saveBtn clickable\" onclick=\"saveRowAjax('" + row.id + "')\">";
}

//Function that sends a fetch request to loggingAjax.php to save a row
function saveRowAjax(i)
{
//https://kennethscoggins.medium.com/how-to-intercept-html-form-submit-with-javascript-for-your-own-uses-2dd22b36d46

    //on page load -> add event listeners to each row's form that changes edit icon to save with appropriate funciton
    //whenever save button pressed, grab all input/textarea values within said function
    // put in json array and do fetch to AJAX on server
    // read response from server, if success move on, if fail JS alert to USEr
    // Method succeeds swap back to edit button, instead of save

    //NOTE HAVE TO CHANGE renameRows() function so that accounts for new listeners as well for the rows
    //Easiest is to clone form and then just replace it with a new one and new eventlisteners (would have to grab currentID and replace form, then give row and form the new IDs)
    //also need some clause so that if save button present, give it a new onclick with ID (just replace old one) otherwise is edit button and can leave alone

    //new rows must be created with save button by default
}
//see loggingAjax.php and fetch API docs for how to use
//I think for getting the data from the page, have to attach a bunch of listeners to each input field in a row, then just grab data from them directly
// unless can intercept form submit with JS?
// If cannot intercept form, need to do form validation to ensure each input has values (except notes)
// Can use 'input' event to see when input/textarea fields are changed

//Function that sends a fetch request to loggingAjax.php to delete a row
function delRowAjax()
{

}

