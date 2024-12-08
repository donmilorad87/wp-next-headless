import Image from "next/image";
import Link from "next/link";
import React from "react";

import Theme from "./Theme";
import MobileNavigation from "./MobileNavigation";
import { cookies } from "next/headers";
const Navbar = async () => {
  const cookieStore = await cookies();
  const session = cookieStore.get("session") ? true : false;

  return (
    <nav className="flex-between background-light900_dark200 fixed z-50 w-full gap-5 p-6 shadow-light-300 dark:shadow-none sm:px-12">
      <Link href="/" className="flex items-center gap-1">
        <Image src="/images/gambling_com_group_logo.jpeg" width={23} height={23} alt="Gambling Logo" />
      </Link>

      <div className="flex-between gap-5">
        <Theme />
        <MobileNavigation session={session} />
      </div>
    </nav>
  );
};

export default Navbar;
