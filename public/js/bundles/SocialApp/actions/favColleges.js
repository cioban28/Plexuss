export function getFavCollege(payloads){
    return{
        type: "GET_FAV_COLLEGES",
        payloads,
    }
}

export function deleteFavCollege(selected) {
    return{
        type: "DEL_FAV_COLLEGES",
        selected: selected,
    }
}