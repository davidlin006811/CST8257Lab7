function ChangeSemester(){
    var link = "CourseSelection.php?selectedSemesterCode=" + $('#selectedSemester').val();
     window.location.replace(link);
} 
function confirmDelete(){
     if (!confirm("Selected registrations will be deleted!")) {
                    return false;
        }
}
