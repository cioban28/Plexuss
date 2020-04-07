export function getApplications(payloads){
    return{
        type: "GET_APPLICATIONS",
        payloads,
    }
}
