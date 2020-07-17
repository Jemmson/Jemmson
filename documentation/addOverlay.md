1. add to the top of the page

<v-overlay :value="overlay">
    <v-progress-circular indeterminate size="64"></v-progress-circular>
</v-overlay>
        
2. add this to the data function
overlay: false,

3. add this to the mounted function
this.overlay = true;

4. add this to the loaded ajax function after the ajax function is complete
this.overlay = false;

5. make sure that all v-if data that is loaded is returned otherwise add this
a. data section
    i. dataReturned: false
b. after ajax is loaded
    i. this.dataReturned = true;
c. in template with the v-if
    i. && dataReturned"