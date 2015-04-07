#!/bin/bash

trap 'kill $(jobs -p)' EXIT
for f in "$@"; do
  tail -n 10 "$f" &
done
wait