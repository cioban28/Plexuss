export function getTrashCollege(payloads){
    return{
        type: "GET_TRASH_COLLEGES",
        payloads,
    }
}

export function deleteTrashCollege(selected, single) {
    return{
        type: "DEL_TRASH_COLLEGES",
        selected: selected,
        single: single,
    }
}