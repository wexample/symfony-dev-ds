`wexample/symfony-dev-ds` is the design-system side of `wexample/symfony-dev`, registered in the `dev` and `test` environments like it. It adds one entry to the development menu of `symfony-design-system`: « reload the demonstration data », which posts to `symfony-dev`'s `dev_seed` route.

The entry sits at the end of the account actions. It is absent when the application declares no `SeederInterface` or does not import `@WexampleSymfonyDevBundle/Resources/config/routes.yaml`; it is shown disabled while nobody is signed in; otherwise it asks for confirmation, holds the page under a spinner while the database is filled, and lands on the sign-in, since the accounts were replaced too.

Its labels are translation keys of `assets/common/dev_menu.trans.yml`, in English and French.
