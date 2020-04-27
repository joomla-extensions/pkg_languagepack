## Language Pack Component
Component for the downloads site to allow creation of language packs. This component assumes that you have installed
[Akeeba Release system](https://github.com/akeeba/release-system) (ARS) already on your system to render the releases.
It then tries to zips generated through them through the ARS helpers to Amazon S3.

### Background
In Joomla we have chosen Akeeba Release system for providing and tracking downloads on our
[downloads site](https://downloads.joomla.org). However we need a place where our language translation teams can upload
packs for their country. This package largely aims to fufill that need by providing a frontend interface for teams to
upload specific language packs.

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
- Fix the router
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
`zip pkg_languagepack.zip pkg_languagepack.xml com_languagepack.zip`

Then install this into Joomla through the extension manager.
