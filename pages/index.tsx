import { collection, doc, getDocs, query, updateDoc } from 'firebase/firestore';
import { useEffect, useState } from 'react';
import { FBfirestore } from '../firebase/initFirebase';
import { calculateRecipient } from '../utils/calculateRecipient';
import { User } from '../utils/types';

const capitalise = (text: string) =>
  text.slice(0, 1).toUpperCase() + text.slice(1);

const reportError = () => {
  alert(
    'Oops, something went wrong! Refresh the page and try again, or contact Robbie if this keeps happening.',
  );
};

export default function Home() {
  const [isLoadingUsers, setIsLoadingUsers] = useState(false);
  const [users, setUsers] = useState<User[] | undefined>();
  const [currentUser, setCurrentUser] = useState<User | undefined>();
  const [dealtRecipient, setDealtRecipient] = useState<User | undefined>();

  const unchosenUsers = users?.filter((u) => !u.chosen);
  const usersWhoHaventMadeAChoice = users?.filter((u) => !u.choiceMade);

  useEffect(() => {
    if (!users && !isLoadingUsers) {
      setIsLoadingUsers(true);

      // Create a reference to the cities collection
      const usersRef = collection(FBfirestore, 'users');
      // Create a query against the collection.
      const q = query(usersRef);

      const usersSnapshot = getDocs(q);
      usersSnapshot.then((docs) => {
        const loadedUsers = [];
        docs.forEach((doc) => {
          loadedUsers.push({ name: doc.id, ...doc.data() });
        });
        setUsers(loadedUsers);
        setIsLoadingUsers(false);
      });
    }
  }, [users, isLoadingUsers]);

  const onChoiceMade = async () => {
    const recipient: User | undefined = calculateRecipient(
      unchosenUsers,
      currentUser,
    );

    if (!recipient)
      alert(
        'You have already made a selection, so something has gone wrong. Please contact Robbie!',
      );

    // Update the currentUser Firebase Doc to choiceMade=true
    const currentUserRef = doc(FBfirestore, 'users', currentUser.name);
    await updateDoc(currentUserRef, {
      choiceMade: true,
    }).catch(() => reportError());

    // Update the dealtRecipient Firebase doc to chosen=true
    const dealtRecipientRef = doc(FBfirestore, 'users', recipient.name);
    await updateDoc(dealtRecipientRef, {
      chosen: true,
    }).catch(() => reportError());

    setDealtRecipient(recipient);
  };

  const reset = () => {
    const userNames = users.map(({ name }) => name);
    const promises = userNames.map((name) => {
      const currentUserRef = doc(FBfirestore, 'users', name);
      return updateDoc(currentUserRef, {
        choiceMade: false,
        chosen: false,
      });
    });

    Promise.all(promises).then(() => alert('RESET'));
  };

  return (
    <>
      <video id="bg-vid" autoPlay muted loop playsInline>
        <source src="christmas.mp4" type="video/mp4" />
      </video>
      <div className="flex">
        <div className="inner">
          <h1>Secret Santa {new Date().getFullYear()}</h1>
          {dealtRecipient ? (
            <div id="choose-name" className="result">
              <p>You are the secret santa for...</p>
              <p className="result-name">{capitalise(dealtRecipient.name)}</p>
              <a
                href={`mailto:?subject=Secret Santa 2016&body=You are the secret santa for ${capitalise(
                  dealtRecipient.name,
                )}`}
                target="_blank"
                rel="noreferrer"
              >
                Email To Myself
              </a>
            </div>
          ) : usersWhoHaventMadeAChoice?.length ? (
            <>
              <p>What&apos;s your name?</p>
              <ul>
                {usersWhoHaventMadeAChoice?.map((user) => (
                  <li
                    key={user.name}
                    className={`name ${
                      currentUser?.name === user.name ? 'chosen' : ''
                    }`}
                    onClick={() => setCurrentUser(user)}
                  >
                    {capitalise(user.name)}
                  </li>
                ))}
              </ul>
              {currentUser && (
                <button id="choose-name" onClick={onChoiceMade}>
                  Submit{' '}
                  <i className="fa fa-arrow-right" aria-hidden="true"></i>
                </button>
              )}
            </>
          ) : (
            <p>
              Everybody has been assigned their secret santa. Merry Christmas!
            </p>
          )}
        </div>
        <button onClick={() => reset()}>RESET</button>
      </div>
    </>
  );
}
