# 3D Print Management Web App Project

index.php
Redirect to UVic Login page. Checks to see if user exists in current database else conducts LDAP lookup

admin-dashboard.php
Home screen of the admin side. Displays active jobs by status. Has links to all other admin pages and the personal dashboard.  Only accessible to admin level users.

admin-edit-group.php
Allows admin to add & remove jobs to a group and to price and change the status of a group.

admin-job-specification.php
Individual job page. Displays data from the database on the specified job by job id. Allows downloading 3d files if they exist. Editable data: status, price(when status is submitted/pending_payment), secondary 3d file (max 200MB), all specifications, copies, material type, staff comments. Sends email when status is pending payment or completed if enable email is checked. Only accessible to admin level users.

admin-manage-printers.php
Displays printer details and can save changes to printer status and extruder colors or material (string data type). Has link to filament stock file. Only accessible to admin level users.

admin-manage-users.php
Displays users info: id, name, netlink, user type & email. Can search by name & netlink and by admin specific. Default is all admins. Has link to user-specifications Only accessible to admin level users.

admin-print-history.php
Lists archived, cancelled, and completed jobs. Can search by date or netlink id. Has links to job specification. Only accessible to admin level users.

admin-reports.php
Shows Moneris response fields. Can search by transaction date, order id  (which is netlink-time-job id), or by successful transactions. Has download csv button that converts data on page to a csv file named 3D_print_Moneris_report.csv. Only accessible to admin level users.

admin-user-specification.php
Displays and offers editable data from specific user in user database. Editable data is user type, email, email reports (automated daily Moneris report emailed). Only accessible to admin level users.

create-group.php
Creates a group from a list of users who have a job with a "submitted" status.

customer-dashboard.php
Displays all jobs linked to logged in netlink user. Has links to all linked specific job pages, new-job creation, and FAQ page. If user is admin, displays link to admin page.

customer-job-information.php
Displays all job details. If file exits allows user to download original file only. If status is pending payment, link appears to go to Moneris payment site. Accessible by netlink owner & admins.

customer-new-job.php
Creates new job. Maximumfile upload size is 200MB. Has links to sub-sections in FAQ. Once submitted, user is emailed confirmation of job creationg.

auth-sec-php
Checks if user is logged in and redirects to index if not logged in, declares session variables, and logs out if requested.

error.php
If LDAP fails and user cannot log in, redirects to this page.

db_create.SQL
database structure.

db.php
Database connection and PDO link.

uploads(folder)
File save location.

jobs_cron(folder)
Contains cron job files

  dsc-archive-jobs.php
  changes status of completed jobs to archived after a specified number of days. Intended to be done daily.

  dsc-canceled-jobs.php
  Checks for payment pending jobs that have been in that status for a specified number of days and changes status to cancelled. Also deletes all associated files. Intended to be done daily.

  dsc-delete-old-files.php
  Checks archived jobs that are between a specified date range and deletes associated files from the server.  Intended to be done weekly.

  dsc-moneris-daily.php
  Compiles a list of successfully transaction from the previous day into an email and sends the email to anyone who has reports checked. Intended to be done daily.

  dsc-pmt-reminder.php
  Searches for jobs that have been pending payment for a specified number of days and emails a reminder to the job owner. Intended to be done daily.

moneris(folder)
Moneris landing and setup files.

  moneris/approved.php
  Saves moneris fields to database. Changes job status to paid.

  moneris/declined.php
  Saves moneris fields to database.

  moneris/cancelled.php
  landing page for cancelled transaction, no moneris fields   returned to save,

  moneris/error.php
  landing page for moneris errors.
