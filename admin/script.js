//Search issue table
function searchIssueTable() {
    var inputIssueID, inputTitle, inputCategory, inputDesc, inputUserID, inputAdmin, inputStatus, inputDel,
        table, tr, td, i,
        filterIssue, filterTitle, filterCategory, filterDesc, filterUser, filterAdmin, filterStatus, filterDel;

    inputIssueID = document.getElementById("issueidInput");
    inputTitle = document.getElementById("titleInput")
    inputCategory = document.getElementById("categoryInput");
    inputDesc = document.getElementById("descInput");
    inputUserID = document.getElementById("useridInput");
    inputAdmin = document.getElementById("adminInput");
    inputStatus = document.getElementById("statusInput");
    inputDel = document.getElementById("deletionInput");

    filterIssue = inputIssueID.value.toUpperCase();
    filterTitle = inputTitle.value.toUpperCase();
    filterCategory = inputCategory.value.toUpperCase();
    filterDesc = inputDesc.value.toUpperCase();
    filterUser = inputUserID.value.toUpperCase();
    filterAdmin = inputAdmin.value.toUpperCase();
    filterStatus = inputStatus.value.toUpperCase();
    filterDel = inputDel.value.toUpperCase();

    table = document.getElementById("issue_table");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        td1 = tr[i].getElementsByTagName("td")[1];
        td2 = tr[i].getElementsByTagName("td")[2];
        td3 = tr[i].getElementsByTagName("td")[3];
        td4 = tr[i].getElementsByTagName("td")[4];
        td5 = tr[i].getElementsByTagName("td")[5];
        td6 = tr[i].getElementsByTagName("td")[6];
        td9 = tr[i].getElementsByTagName("td")[9];

        if (td && td1 && td2 && td3 && td4 && td5 && td6 && td9) {
            issue = (td.textContent || td.innerText).toUpperCase();
            title = (td1.textContent || td1.innerText).toUpperCase();
            cat = (td2.textContent || td2.innerText).toUpperCase();
            desc = (td3.textContent || td3.innerText).toUpperCase();
            user = (td4.textContent || td4.innerText).toUpperCase();
            admin = (td5.textContent || td5.innerText).toUpperCase();
            stat = (td6.textContent || td6.innerText).toUpperCase();
            del = (td9.textContent || td9.innerText).toUpperCase();

            if (
                issue.indexOf(filterIssue) > -1 &&
                title.indexOf(filterTitle) > -1 &&
                cat.indexOf(filterCategory) > -1 &&
                desc.indexOf(filterDesc) > -1 &&
                user.indexOf(filterUser) > -1 &&
                admin.indexOf(filterAdmin) > -1 &&
                stat.indexOf(filterStatus) > -1 &&
                del.indexOf(filterDel) > -1
            ) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }

    }
}
