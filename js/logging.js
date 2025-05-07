/*
*   These functions created by Nick for the logging page.
*/

//Function that allows user to dynamically add a row to the log table
async function addExercise(){
    let table = document.getElementById('logTable');
    let newRow = table.insertRow();                     //insert row at end of table
    
    //set ID of new row accordingly - added directly at end of table
    let count = countRows();
    newRow.id = "row" + count;

    //Get <select> with all options for exercises
    let data = {
        "action" : "getExercises",
        "rowID" : newRow.id
    }

    //JS fetch call to PHP 
    const request = new Request("scriptsPHP/loggingAjax.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    });

    let selectString;
    try
    {
        const response = await fetch(request);

        if(!response.ok) {                          //bad status response from server (not in 2xx range)
            throw new Error(`Response status: ${response.status}`);
        }

        let serverJson = await response.json();     //response from server in JSON: check for errors and get <select> payload
        let msg = serverJson['msg'];
        if(msg !== "Success") throw new Error(msg);

        selectString = serverJson['select'];
    }
    catch (error)
    {
        console.error(error.message);
    }

    //finally, give new row the proper HTML for functionality
    newRow.innerHTML ="<td>" + selectString + "</td>" + 
                    "<td> <input class=\"form-control inputNum\" type=\"number\" name=\"sets\" value=\"0\" required min=0 oninput=\"showUnsaved('" + newRow.id + "')\"/> " +
                         "<input type=\"hidden\" name=\"eeid\" value=\"\"/> </td>" + 
                    "<td> <input class=\"form-control inputNum\" type=\"number\" name=\"reps\" value=\"0\" required min=0 oninput=\"showUnsaved('" + newRow.id + "')\"/> </td>" + 
                    "<td> <input class=\"form-control inputNum\" type=\"number\" name=\"weight\" value=\"0\" required min = 0 oninput=\"showUnsaved('" + newRow.id + "')\"/> </td>" + 
                    "<td> <input class=\"form-control inputNum\" type=\"number\" name=\"duration\" value=\"0\" required min=0 oninput=\"showUnsaved('" + newRow.id + "')\"/> </td>" + 
                    "<td> <textarea class=\"form-control inputText\" name=\"notes\" placeholder=\"notes\" oninput=\"showUnsaved('" + newRow.id + "')\"></textarea></td>" + 
                    "<td class=\"modifyTD\"> <i class=\"bi bi-floppy saveBtn clickable\" onclick=\"save('" + newRow.id + "')\"></i>  </td>" +
                    "<td class=\"modifyTD\"> <i class=\"bi bi-trash delBtn clickable\" onclick=\"openModal('" + count + "')\"></i>  </td>";
}


//Function that deletes a row out of a the log table
function deleteExercise(rowID){
    //delete from database
    let row = document.getElementById('row' + rowID);
    delRowAjax(row);

    //delete row on page
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
async function saveRowAjax(row)
{
    let data = {
        "action" : "saveRow",
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

    let eeid;
    try
    {
        const response = await fetch(request);

        if(!response.ok) {                          //bad status response from server (not in 2xx range)
            throw new Error(`Response status: ${response.status}`);
        }

        let serverJson = await response.json();     //response from server in JSON, to check for DB errors that do not cause server to fail
        let msg = serverJson['msg'];
        eeid = serverJson['eeid'];
        if(msg !== "Success") throw new Error(msg);
    }
    catch (error)
    {
        console.error(error.message);
        return;
    }


    alert("Row saved.");

    //change save back to edit button
    row.cells[6].innerHTML = "<i class=\"bi bi-pencil editBtn\" onclick=\"save('" + row.id + "')\">";
    
    //must get eeid hidden input and add the eeid to it, from the database
    let eeidInput = row.cells[1].children[1];
    eeidInput.value = eeid;
}


//Function that sends a fetch request to loggingAjax.php to edit a row in the DB
async function editRowAjax(row)
{
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
        return;
    }


    alert("Row saved.");

    //change save back to edit button
    row.cells[6].innerHTML = "<i class=\"bi bi-pencil editBtn\" onclick=\"save('" + row.id + "')\">";
}

//Function that sends a fetch request to loggingAjax.php to delete a row
async function delRowAjax(row)
{
    //handle edge case - new row added to page, but never saved before delete (nothing to delete from DB)
    let eeid = row.cells[1].children[1].value;
    if(!eeid)
    {
        return;
    } 


    let data = {
        "action" : "delRow",
        "eeid" : eeid
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
        return;
    }
}

//Function to handle dynamically displaying input fields in create log form
function updateField()
{
    //get selected radio button in form
    let selectedBtn = document.querySelector('input[name="create"]:checked');
    let id = selectedBtn.id;

    //get divs
    let templateDiv = document.getElementById("templateSelect");
    let previousDiv = document.getElementById("previousSelect");

    switch(id)
    {
        case "scratch":
            //hide other two divs (in case either is shown)
            templateDiv.style.display = "none";
            previousDiv.style.display = "none";
            break;
        case "template":
            //display template div, hide other 
            templateDiv.style.display = "block";
            previousDiv.style.display = "none";
            break;
        case "previous":
            //display previous div, hide other
            templateDiv.style.display = "none";
            previousDiv.style.display = "block";
            break;
        default:
            break;              //do nothing

    }

}

