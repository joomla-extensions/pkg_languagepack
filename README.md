## Language Pack Component
Component for the downloads site to allow creation of language packs. This component assumes that you have installed
[Akeeba Release system](https://github.com/akeeba/release-system) (ARS) already on your system to render the releases.
It then tries to zips generated through them through the ARS helpers to Amazon S3.

### Background
Joomla used to host all it's downloads on Joomla Code. However since the project moved from SVN to Git and Github the
website has not been updated and we are currently struggling to keep it up.

We chose Akeeba Release system for providing and tracking CMS Downloads on our [downloads site](https://downloads.joomla.org)
and migrated to this in 2017 (you can find the code powering the downloads site [on Github](https://github.com/joomla/downloads.joomla.org)).
However we still needed a place where our language translation teams can upload language packs to complete the migration
from Joomla Code. This extension fufills that need by providing a frontend interface for teams to upload specific
language packs into Akeeba Release system.

In the future it's anticipated that the [main translations page](https://community.joomla.org/translations.html) will
instead be on the downloads site and using this component for a frontend. 

### Implementation Information
#### Applications
This components creates a concept of Applications which are the equivalent of the ARS Environment. An application could
be either a Joomla Core Version (e.g. Joomla 3) or a component (e.g. Weblinks). The Application is what determines the
path in the ARS bucket.

#### Languages
Languages in this component correspond directly to an ARS Category. A Language should have a User Group in Joomla
associated with it. The members of this user group are the ones allowed to upload a language pack into the ARS category.
Languages can be uploaded from different sources. Currently the only supported source is a file upload which mimics
the old behaviour from Joomla Code. However the intention is to have a factory with multiple helpers in the future
(see future goals for more Information).

#### Releases
Joomla keeps a log of any created language packs in the language packs table going forward to track releases. In the
future it's possible it may be better to replace this through an action log plugin into the central table.

### Future Goals
- Fix TODO's left in the code. This component was built with a tight MVP in mind and there are many improvements that
can still be made
- Direct integration with crowdin to download packs straight from crowdin
- Integration with action logs plugin (maybe replacing the releases table)
- Administrator Interface for adding both applications and languages to be used by the TT Team coordinators
- Remove hardcoded inserts in the SQL File to allow it to be used generically (currently a few inserts are built
specifically for the Downloads Site environment)
- Any alternative helps (e.g. GitHub listeners)

### How to install this on your site
This component assumes that you have an existing install of ARS on your system. That needs to be installed before
this component is installed else the foreign key checks will fail.

To build this component run `zip -r com_languagepack.zip com_languagepack` then run
`zip pkg_languagepack.zip pkg_languagepack.xml com_languagepack.zip` or for an all in 1 command:
`rm -f pkg_languagepack.zip && zip -r com_languagepack.zip com_languagepack && zip pkg_languagepack.zip pkg_languagepack.xml com_languagepack.zip && rm com_languagepack.zip`

Then install this into Joomla through the extension manager.

If you are running this locally with a copy of ARS then you may wish to remove the insert into the `#__languagepack_applications`
table from install_mysql.sql so that there are no foreign keys on the ARS environment table on initial setup
