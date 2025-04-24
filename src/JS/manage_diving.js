let selectedId = null;

function openConfirmModal(id)
{
    selectedId = id;
    document.getElementById('confirmModal').style.display = 'flex';
}

function closeConfirmModal()
{
    selectedId = null;
    document.getElementById('confirmModal').style.display = 'none';
}

function confirmDelete()
{
    if(selectedId !== null)
    {
        window.location.href = 'delete_diving_plan.php?id=' + selectedId;
    }
}