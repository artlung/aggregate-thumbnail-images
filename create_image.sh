 #!/bin/bash -e

# Generates an image file 600x600 based on data from a WordPress site
# With the installed plugin Aggregate Thumbnail Images
# call the script with ./create_image.sh --taxonomy=category --name=programming
# call the script with ./create_image.sh --taxonomy=tag --name=swim2024
# call the script with ./create_image.sh --taxonomy=category --id=1234
# call the script with ./create_image.sh --taxonomy=tag --id=783


# check to see that jq exists and if not fail with message to install it
if ! [ -x "$(command -v jq)" ]; then
  echo 'Error: jq is not installed.' >&2
  echo 'Please install jq: https://stedolan.github.io/jq/download/' >&2
  exit 1
fi

API_PATH="/wp-json/aggregate-thumbnail-images/v1"

# get the endpoint site from a file in home directory called .aggregate-thumbnail-images
SITE=$(cat ~/.aggregate-thumbnail-images)

ENDPOINT="$SITE$API_PATH"



# if it does not exist, fail with message to create it
if [ -z "$ENDPOINT" ]; then
  echo 'Error: Endpoint not set.' >&2
  echo 'Please create a file in your home directory called .aggregate-thumbnail-images with the URL of the site you want to use.' >&2
  exit 1
fi



for i in "$@"
do
case $i in
    --taxonomy=*)
    TAXONOMY="${i#*=}"
    shift # past argument=value
    ;;
    --name=*)
    NAME="${i#*=}"
    shift # past argument=value
    ;;
    --id=*)
    ID="${i#*=}"
    shift # past argument=value
    ;;
    *)
          # unknown option
    ;;
esac
done

if [ "$TAXONOMY" == "category" ]; then
  URL="$ENDPOINT/categoryByName/$NAME"
elif [ "$TAXONOMY" == "tag" ]; then
  URL="$ENDPOINT/tagByName/$NAME"
fi

if [ "$ID" ]; then
  if [ "$TAXONOMY" == "category" ]; then
    URL="$ENDPOINT/categoryById/$ID"
  elif [ "$TAXONOMY" == "tag" ]; then
    URL="$ENDPOINT/tagById/$ID"
  fi
fi

echo "URL: $URL"

#execute and store temporarily
curl -o images.json $URL;

#json should look like this:
# {"images":["https:\/\/cdn.artlung.com\/blog\/wp-content\/uploads\/2024\/02\/Screenshot-2024-02-26-at-2.28.06xE2x80xAFPM.png","https:\/\/cdn.artlung.com\/blog\/wp-content\/uploads\/2024\/02\/Screenshot-2024-02-16-at-9.01.41xE2x80xAFPM.jpeg","https:\/\/cdn.artlung.com\/blog\/wp-content\/uploads\/2024\/01\/header-2004-scale-down.jpeg","https:\/\/cdn.artlung.com\/blog\/wp-content\/uploads\/2023\/09\/gist.jpeg","https:\/\/cdn.artlung.com\/blog\/wp-content\/uploads\/2020\/02\/indieweb-sandiego-homebrew-website-club.png","https:\/\/cdn.artlung.com\/blog\/wp-content\/uploads\/2023\/04\/250F9136-7AEB-4AEA-9F05-CE9FFDC30BDD.jpg","https:\/\/cdn.artlung.com\/blog\/wp-content\/uploads\/2022\/05\/screenshot-131124527043592360.png","https:\/\/cdn.artlung.com\/blog\/wp-content\/uploads\/2022\/02\/c-plus-plus-compiling-2022.jpg","https:\/\/cdn.artlung.com\/blog\/wp-content\/uploads\/2022\/01\/spielberg-spreadsheet.jpg"],"filename":"category_programming_600x600.jpg","exists":{"path":"https:\/\/cdn.artlung.com\/blog\/wp-content\/uploads\/aggregate-thumbnail-images\/category_programming_600x600.jpg","file_exists":false}}

# If it is not json, fail with message
if ! jq -e . >/dev/null 2>&1 <<<"$(cat images.json)"; then
  echo "Error: Not a valid json response" >&2
  exit 1
fi

# parse the json
EXISTS=$(jq -r '.exists.file_exists' images.json)
if [ "$EXISTS" == "true" ]; then
  echo "File already exists"
  # cleanup
  rm images.json;
  exit 0
fi


IMAGES=$(jq -r '.images[]' images.json)
FILENAME=$(jq -r '.filename' images.json)


# curl down each image as image1.jpg, image2.jpg, etc.
i=1
for IMAGE in $IMAGES
do
  curl -o image$i.jpg $IMAGE;
  convert image$i.jpg -resize 200x200 image$i.jpg;
  i=$((i+1))
done

# create the image
convert -size 600x600 xc:white \
  -page +0+0 image1.jpg \
  -page +200+0 image2.jpg \
  -page +400+0 image3.jpg \
  -page +0+200 image4.jpg \
  -page +200+200 image5.jpg \
  -page +400+200 image6.jpg \
  -page +0+400 image7.jpg \
  -page +200+400 image8.jpg \
  -page +400+400 image9.jpg \
  -flatten $FILENAME;


# cleanup
rm image*.jpg;
rm images.json;
