Yii Framework 2 imagine extension Change Log
================================================

2.1.0 November 3, 2016
----------------------

- Enh #2: ImageInterface objects are now supported as image files (samdark)
- Enh #11: Resources are now supported as image files (samdark)
- Enh #20: Upgraded the imagine library from 0.5.x to 0.6.x.
      In order to upgrade to 0.6.x the color behavior had to be
      changed. In addition a new `autorotate` method has been implemented
      in order to rotate images based in the EXIF informations provided
      inside the image (nadar)

2.0.4 September 4, 2016
-----------------------

- Enh #3: `Image::thumbnail()` could now automatically calculate thumbnail dimensions based on aspect ratio of original
  image if only width or only height is specified. `Image::$thumbnailBackgroundColor` and
  `Image::$thumbnailBackgroundAlpha` are introduced to be able to configure fill color of thumbnails (HaruAtari, samdark)

2.0.3 March 01, 2015
--------------------

- no changes in this release.


2.0.2 January 11, 2015
----------------------

- no changes in this release.


2.0.1 December 07, 2014
-----------------------

- no changes in this release.


2.0.0 October 12, 2014
----------------------

- no changes in this release.


2.0.0-rc September 27, 2014
---------------------------

- no changes in this release.


2.0.0-beta April 13, 2014
-------------------------

- Initial release.
