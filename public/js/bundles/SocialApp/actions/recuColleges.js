import SingleCollegeAdditionalInfo from "../../StudentApp/components/College_Application/SingleCollegeAdditionalInfo";

export function getRecuCollege(payloads){
    return{
        type: "GET_RECU_COLLEGES",
        payloads,
    }
}

export function deleteRecuCollege(selected, single) {
    return{
        type: "DEL_RECU_COLLEGES",
        selected: selected,
        single: single, 
    }
}