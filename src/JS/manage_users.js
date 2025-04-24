let deleteUserId = null;
function openConfirmModal(userId)
{
    deleteUserId = userId;
    document.getElementById("confirmModal").style.display = "flex";
}

function closeConfirmModal()
{
    document.getElementById("confirmModal").style.display = "none";
    deleteUserId = null;
}

function proceedDelete()
{
    if(deleteUserId !== null)
    {
        window.location.href = `delete_user.php?id=${deleteUserId}`;
    }
}
