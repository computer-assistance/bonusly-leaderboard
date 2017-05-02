## Synopsis

Bonusly leaderboard is an open source Laravel (5.4) project developed in-house at Computer Assistance, Oxford, UK after we signed up for Bonusly (https://bonus.ly/ - peer to peer team-member recognition and rewards).

We realised early on that the bonus notifications provided by Bonusly were not going to suit our needs so rolled this project out in the space of a week or so.

## Example
<img src="https://github.com/computer-assistance/bonusly-leaderboard/blob/master/bonusly-thumb-300x190.png" style="text-align:center;">

The app makes 2 calls to the Bonusly api
 1. Retrieve user data.
 2. Retrieve monthly bonus data.

This data is then analysed by the application, summed and sorted and pushed out to view genrator.

## Motivation

We felt that this was a much-needed application as far as our organization was concerned and feel this adds an at-a-glance, real-time feature, thereby adding value to our Bonusly usage thus enhancing their project's worth to us.

## Installation

### Preliminaries

 1. Sign up for Bonusly for your organisation/team.
 2. An API key is required and is easily setup using their API dashboard at https://bonusly.gelato.io/ (you must sign in to your developer account.)
 3. Laravel 5.4


