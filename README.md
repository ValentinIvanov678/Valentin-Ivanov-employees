# Valentin-Ivanov-employees
A prototype employee statistics app for and interview with Sirma.

---

## Setup instructions
### Prerequisites 
You will need to have **PHP** installed on your machine.
### Starting the app
1. In you terminal run the command `php -S localhost:8080`
2. Open your browser and go to your [localhost](http://localhost:8080)

## Decisions & things worth noting
* The UI was mostly generated with AI. 
**Reasoning:**
    * Creating and styling the UI by hand would've taken a decent amount of time.
    * I validated and read all generated code for mistakes during generation.
    * Changes needed to the UI after generation were made by me.
    * This app was created as part of my application for a Backend developer.
* Since the app is relatively simple its architecture and the way things work are also relatively simple. I see no reason to over engineer an app just for the sake of complexity.
* All Date Formats I can think of are covered and can be handled. Mixed Timestamps in one file is also possible.
* All CSV files we generated with AI. Making them by hand seemed like a waste of time.  
* All CSV files contain the same data with changed Date Format. **Exception:** `employee_projects_mixed_timestamps.csv` contains a compilation of 10 record exports from all other files.
* The user can select a CSV File from the server or upload one from their machine. I was not sure which approach was expected from the brief so I made both.
* The entire BackEnd of the app was written by me. AI was used as a reseach tool.

## Git History
I made the mistake of starting work before I made the Git Repository. I decided to finish the app and have the entire code in one single commit because it was cleaner. Below is a Step by Step representation of how the development happened. 

1. I generated one CSV File. 
2. I started working on the Back end creating index.php and api.php
3. Made the entire algorithm for the statistics to work with the generated file. 
4. Generated all other CSV Files. 
5. Tested the entire backend.
6. Generated the UI. 
7. Created a Router(router.php).
8. Connected the Frontend with the backend. 
9. Tested the app and made some UI improvements. 
10. Added functionality for the user to upload own CSV file.
11. Created a new CSV file with all Date Formats present. 
12. Refactored code to handle mixed formats.
13. Bug fixing and final touches. 
14. Created Documentation.

## Possible improvements
* Scale up the router and separate the visualisation logic into a separate controller or move the visualisation logic entirely to a different service. 
* Add user accounts and user authentication. 
* Add a data layer to save user interaction and data. 
* Add tenants.
* Improve the UI. 
* Lean more into the core principles of OOP like inheritance, abstraction, polymorphism and proper encapsulation as the APP gets bigger and more complex. 
* Add a proper Data layer. 