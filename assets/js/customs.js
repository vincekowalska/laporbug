function edit_profile_profile_page() 
{
    var $name = document.getElementById("name");
    var $role = document.getElementById("role");
    var $email = document.getElementById("email");
    var $phone_number = document.getElementById("phone_number");
    var $edit_btn = document.getElementById("edit-btn");
    var $save_btn = document.getElementById("save-btn");
    
    $name.removeAttribute('readonly');
    $role.removeAttribute('disabled');
    $email.removeAttribute('readonly');
    $phone_number.removeAttribute('readonly');
    
    $edit_btn.hidden = true;
    $save_btn.hidden = false;
}

function save_profile_profile_page() 
{
    var $name = document.getElementById("name");
    var $role = document.getElementById("role");
    var $email = document.getElementById("email");
    var $phone_number = document.getElementById("phone_number");
    var $old_name = document.getElementById("old_name");
    var $old_role = document.getElementById("old_role");
    var $old_email = document.getElementById("old_email");
    var $old_phone_number = document.getElementById("old_phone_number");    
    var $edit_btn = document.getElementById("edit-btn");
    var $save_btn = document.getElementById("save-btn");
    
    $edit_btn.hidden = false;
    $save_btn.hidden = true;
    if (!($name.value == $old_name.value) | !($role.value == $old_role.value) | !($email.value == $old_email.value) |
        !($phone_number.value == $old_phone_number.value)){
            document.getElementById("update-form").submit();
    }
}
function save_profile_edit_page() 
{
    var $name = document.getElementById("name");
    var $role = document.getElementById("role");
    var $email = document.getElementById("email");
    var $phone_number = document.getElementById("phone_number");
    var $password = document.getElementById('password');
    var $old_name = document.getElementById("old_name");
    var $old_role = document.getElementById("old_role");
    var $old_email = document.getElementById("old_email");
    var $old_phone_number = document.getElementById("old_phone_number");
    var $old_password = document.getElementById('old_password');
    
    if (!($name.value == $old_name.value) | !($role.value == $old_role.value) | !($email.value == $old_email.value) |
        !($phone_number.value == $old_phone_number.value) | !($password.value == $old_password.value)){
            document.getElementById("update-form").submit();
    }
}

function edit_finding() 
{
    var $id = document.getElementById("id");
    var $save_btn = document.getElementById('save-btn');   
    var $cancel_btn = document.getElementById('cancel-btn');
    var $edit_btn = document.getElementById('edit-btn');
    
    var $asset_name = document.getElementById("asset_name");
    var $title = document.getElementById('title');
    var $description = document.getElementById("description");
    var $severity = document.getElementById("severity");
    var $poc_video_url = document.getElementById("poc_video_url");
    var input_poc_video_url = document.getElementById("input_poc_video_url");
    var $new_proofOfConcept = document.getElementById("new_proofOfConcept");
    var empty_poc = document.getElementById("empty_poc");
    
    $save_btn.hidden = false;
    $cancel_btn.hidden = false;
    $edit_btn.hidden = true;

    $asset_name.disabled = false;
    $severity.disabled = false;
    $new_proofOfConcept.hidden = false;
    if ($poc_video_url) {
        $poc_video_url.hidden = true;
    }else{
        empty_poc.hidden = true;    
    }
    input_poc_video_url.hidden = false;
    input.removeAttribute('readonly',true);
    
}
function cancel_edit_finding() 
{
    var empty_poc = document.getElementById("empty_poc");
    var $save_btn = document.getElementById('save-btn');   
    var $cancel_btn = document.getElementById('cancel-btn');
    var $edit_btn = document.getElementById('edit-btn');
    
    var $asset_name = document.getElementById("asset_name");
    var $title = document.getElementById("title");
    var description = document.getElementById("description");
    var $severity = document.getElementById("severity");
    var $poc_video_url = document.getElementById("poc_video_url");
    var $input_poc_video_url = document.getElementById("input_poc_video_url");
    var $new_proofOfConcept = document.getElementById("new_proofOfConcept");
    
    $save_btn.hidden = true;
    $cancel_btn.hidden = true;
    $edit_btn.hidden = false;
    
    $asset_name.disabled = true;
    $severity.disabled = true;
    $new_proofOfConcept.hidden = true;
    $input_poc_video_url.hidden = true;
    if (empty_poc) {
        empty_poc.hidden = false;    
    }else{
        $poc_video_url.hidden = false;
    }

    $title.readonly = true;
    description.readonly = true;
}

function search_finding() {
    // Get input value
    var input = document.getElementById('input-to-find');
    var filter = input.value.toUpperCase();
    
    // Get table rows
    var table = document.querySelector('.table');
    var tr = table.getElementsByTagName('tr');
    
    // Loop through all table rows and hide those that don't match the search query
    for (var i = 0; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName('td')[0]; // Column index where Title is located
        
        if (td) {
            var txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = '';
            } else {
                tr[i].style.display = 'none';
            }
        }
    }
}


