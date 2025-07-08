#!/bin/bash

CHANGELOG_FILE="CHANGELOG.md"
DRY_RUN=false

# 🔧 Controlla se è in modalità dry-run
if [[ "$1" == "--dry-run" || "$1" == "-d" ]]; then
  DRY_RUN=true
fi

# 🏷️ Mostra la versione corrente
if [[ "$1" == "current" ]]; then
  LAST_TAG=$(git describe --tags --abbrev=0 2>/dev/null)
  if [ -z "$LAST_TAG" ]; then
    echo "No release tags found (defaulting to 0.0.0)"
  else
    echo "Current version: ${LAST_TAG#v}"
  fi
  exit 0
fi

# 🔍 Recupera ultimo tag
LAST_TAG=$(git describe --tags --abbrev=0 2>/dev/null)
if [ -z "$LAST_TAG" ]; then
  LAST_TAG="v0.0.0"
fi

echo "🔍 Last release tag: $LAST_TAG"

# 🧾 Log dei commit da ultimo tag
COMMITS=$(git log "$LAST_TAG"..HEAD --pretty=format:"%s")

echo ""
echo "📝 Commits since $LAST_TAG:"
echo "$COMMITS"
echo ""

# 🔮 Suggerisce tipo di bump
suggest_bump() {
  if echo "$COMMITS" | grep -i -q -E 'breaking|BREAKING CHANGE'; then
    echo "major"
  elif echo "$COMMITS" | grep -i -q -E '^feat|add|new'; then
    echo "minor"
  else
    echo "patch"
  fi
}

SUGGESTED=$(suggest_bump)

# 🧮 Calcola nuova versione
IFS='.' read -r MAJOR MINOR PATCH <<< "${LAST_TAG#v}"

case "$SUGGESTED" in
  major)
    ((MAJOR++))
    MINOR=0
    PATCH=0
    ;;
  minor)
    ((MINOR++))
    PATCH=0
    ;;
  patch)
    ((PATCH++))
    ;;
esac

NEXT_VERSION="$MAJOR.$MINOR.$PATCH"
echo "📦 Suggested next version: v$NEXT_VERSION ($SUGGESTED)"

read -p "Enter release version [$NEXT_VERSION]: " INPUT
VERSION=${INPUT:-$NEXT_VERSION}

read -p "⚠️ Confirm release of v$VERSION? [y/N]: " CONFIRM
if [[ "$CONFIRM" != "y" && "$CONFIRM" != "Y" ]]; then
  echo "❌ Release aborted."
  exit 1
fi

# 🧱 Prepara changelog
DATE=$(date +%Y-%m-%d)
HEADER="## [v$VERSION] - $DATE"
TEMP_FILE=$(mktemp)

echo "$HEADER" > "$TEMP_FILE"
echo "" >> "$TEMP_FILE"

# ➕ Added
if echo "$COMMITS" | grep -i -q -E '^feat|add|new'; then
  echo "### Added" >> "$TEMP_FILE"
  echo "$COMMITS" | grep -i -E '^feat|add|new' | sed 's/^/- /' >> "$TEMP_FILE"
  echo "" >> "$TEMP_FILE"
fi

# 🐞 Fixed
if echo "$COMMITS" | grep -i -q -E '^fix|bug'; then
  echo "### Fixed" >> "$TEMP_FILE"
  echo "$COMMITS" | grep -i -E '^fix|bug' | sed 's/^/- /' >> "$TEMP_FILE"
  echo "" >> "$TEMP_FILE"
fi

# 🔁 Changed
if echo "$COMMITS" | grep -i -v -E '^feat|add|new|fix|bug'; then
  echo "### Changed" >> "$TEMP_FILE"
  echo "$COMMITS" | grep -i -v -E '^feat|add|new|fix|bug' | sed 's/^/- /' >> "$TEMP_FILE"
  echo "" >> "$TEMP_FILE"
fi

echo ""
echo "📄 Changelog preview for v$VERSION:"
cat "$TEMP_FILE"
echo ""

if [ "$DRY_RUN" = true ]; then
  echo "🧪 Dry run: no files modified, no git actions performed."
  echo "🔚 End of dry run."
  rm "$TEMP_FILE"
  exit 0
fi

# 📝 Aggiorna changelog
if grep -q "^---" "$CHANGELOG_FILE"; then
  sed -i "0,/^---/a $(cat $TEMP_FILE)" "$CHANGELOG_FILE"
else
  cat "$TEMP_FILE" >> "$CHANGELOG_FILE"
fi

rm "$TEMP_FILE"

read -p "⚠️ Eseguire commit, tag e push per la versione v$VERSION? [y/N]: " FINAL_CONFIRM
if [[ "$FINAL_CONFIRM" != "y" && "$FINAL_CONFIRM" != "Y" ]]; then
  echo "❌ Release aborted."
  exit 1
fi

# 🚀 Commit, tag, push
git add .
git commit -m "Release v$VERSION"
git tag -a "v$VERSION" -m "Release v$VERSION"
git push origin main
git push origin "v$VERSION"

echo "✅ Released v$VERSION and updated CHANGELOG.md"
