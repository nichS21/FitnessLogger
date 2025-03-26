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
}

