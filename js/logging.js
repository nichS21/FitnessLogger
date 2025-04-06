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


        //for first six cells, must change inline 'oninput' function to have new rowID
        for(let i = 0; i < 6; i++)
        {
            currCellInput = currRow.cells[i].children[0];       //first child is the input/textarea field
            currCellInput.removeAttribute('oninput');
            currCellInput.oninput = function() {showUnsaved(currRow.id)};   
        }

        //check if have edit or save button
        editSaveBtn = currRow.cells[6].children[0];             //edit/save button is first and only child of this column
        if(editSaveBtn.classList.contains('bi-floppy'))         //save button
        {
            currRow.cells[6].innerHTML = "<i class=\"bi bi-floppy saveBtn clickable\" onclick=\"save('" + currRow.id + "')\">"
        }
        else{                                                   //edit button
            currRow.cells[6].innerHTML = "<i class=\"bi bi-pencil editBtn\" ></i>";
        }

        //change delete button of this row to have proper new, ID
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


//Function that changes default edit button to save btn for a modified row
function showUnsaved(ID)
{
    let row = document.getElementById(ID);

    row.cells[6].innerHTML = "<i class=\"bi bi-floppy saveBtn clickable\" onclick=\"save('" + row.id + "')\">";
}

//Saves a given row: if eeid is present, then updates a values in database, otherwise inserts into the database
function save(rowID)
{
    let row = document.getElementById(rowID);
    let eeidInput = row.cells[1].children[1];           //second child of the second <td> of a row is the hidden eeid input
    let eeid = eeidInput.value;

    if(!eeid) saveRowAjax(row);                         //not in database already, must insert
    else if (eeid) editRowAjax(row);                    //already in DB, so update it
    else alert("A JS error has occured upon save.");    //unexpected failure
}

//Function that sends a fetch request to loggingAjax.php to save a row
function saveRowAjax(row)
{
    //fetch call
    alert("Row saved");

    //change save button back to edit
    row.cells[6].innerHTML = "<i class=\"bi bi-pencil clickable\" onclick=\"save('" + row.id + "')\">";

    //new row must have eeid added to the second cell


    //on page load -> add event listeners to each row's form that changes edit icon to save with appropriate funciton
    //whenever save button pressed, grab all input/textarea values within said function
    // put in json array and do fetch to AJAX on server
    // read response from server, if success move on, if fail JS alert to USEr
    // Method succeeds swap back to edit button, instead of save


    //new rows must be created with save button by default
}
//see loggingAjax.php and fetch API docs for how to use
// If cannot intercept form, need to do form validation to ensure each input has values (except notes)

//Function that sends a fetch request to loggingAjax.php to edit a row in the DB
async function editRowAjax(row)
{
    //create form validation function? 

    let data = {
        "action" : "updateRow",
        "eeid" : row.cells[1].children[1].value,
        "lid" : document.getElementById('lid').value,
        "eid" : row.cells[0].children[0].value,
        "time" : row.cells[4].children[0].value, 
        "sets" : row.cells[1].children[0].value,
        "reps" : row.cells[2].children[0].value,
        "weight" : row.cells[3].children[0].value,
        "notes" : row.cells[5].children[0].value
    }

    //JS fetch call to PHP 
    const request = new Request("scriptsPHP/loggingAjax.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    });

    try
    {
        const response = await fetch(request);

        if(!response.ok) {                          //bad status response from server (not in 2xx range)
            throw new Error(`Response status: ${response.status}`);
        }

        let serverJson = await response.json();     //response from server in JSON, to check for DB errors that do not cause server to fail
        let msg = serverJson['msg'];
        if(msg !== "Success") throw new Error(msg);
    }
    catch (error)
    {
        console.error(error.message);
    }


    alert("Row saved.");

    //change save back to edit button
    row.cells[6].innerHTML = "<i class=\"bi bi-pencil editBtn\" onclick=\"save('" + row.id + "')\">";
}

//Function that sends a fetch request to loggingAjax.php to delete a row
function delRowAjax(row)
{
    ///have to link back with deleteExercise function and the modal
}

