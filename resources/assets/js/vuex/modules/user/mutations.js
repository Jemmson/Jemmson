export const setUser = (state, response) => {
  console.log(JSON.stringify(response));
  state.user = response;
}