import { User } from './types';

const randomIntFromInterval = (min: number, max: number) =>
  Math.floor(Math.random() * (max - min + 1) + min);

/**
    THE LOGIC 
    by Elena Jung

    function ( currentUser: User, allUsers: User[] ) {

      1. Get current users availableChoices

      2. Remove anyone who has already been chosen by others to get unchosenUserName

      3. Get everyone who hasn't choiceMade

      4. Create key:value map of { unchosenUserName: X }

      x = loop through (3), if [unchosenUserName] is in (3).availableChoices, X++

      5. If anyone has pop = 0, choose them as recipient

      6. If nobody has higher, choose a random recipient

    }
   */

const calculatePopularity = (
  globalUsersWithoutChoiceMade: User[],
  unchosenUserName: string,
): number =>
  globalUsersWithoutChoiceMade.reduce((acc, obj) => {
    if (obj.possibleChoices.includes(unchosenUserName)) {
      return acc + 1;
    }
    return acc;
  }, 0);

export const calculateRecipient = (
  allUsers: User[],
  currentUser: User,
): User => {
  const currentUserUnchosenPossibleChoicesObjs = currentUser.possibleChoices // Get current users list of possible choices
    .map((choiceName) => allUsers.find(({ name }) => choiceName === name)) // Convert them into user objects
    .filter(({ chosen }) => !chosen); // Remove the ones which have been chosen

  const allUsersWhoHaventMadeAChoiceYet = allUsers.filter(
    ({ choiceMade }) => !choiceMade,
  );

  const popularityMapping: { [key: string]: number } = {};

  currentUserUnchosenPossibleChoicesObjs.forEach(({ name }) => {
    popularityMapping[name] = calculatePopularity(
      allUsersWhoHaventMadeAChoiceYet,
      name,
    );
  });

  // If a user has populatiry of 1, choose them
  const userNameWithPopOne = Object.entries(popularityMapping).find(
    ([, val]) => val === 1,
  )?.[0];

  if (userNameWithPopOne) {
    return allUsers.find(({ name }) => name === userNameWithPopOne);
  }

  // If no user has poppularity 1, return a random user from the users avalable list
  const randomIndex = randomIntFromInterval(
    0,
    currentUserUnchosenPossibleChoicesObjs.length - 1,
  );

  return currentUserUnchosenPossibleChoicesObjs[randomIndex];
};
