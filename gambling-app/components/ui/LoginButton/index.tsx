import React from "react";

import ROUTES from "@/constants/routes";
import { Button } from "../button";
import Link from "next/link";
import Image from "next/image";
const LoginButton = ({ session }: { session: boolean }) => {
  return (
    <>
      {!session && (
        <Button className="small-medium btn-secondary min-h-[41px] w-full rounded-lg px-4 py-3 shadow-bibe" asChild>
          <Link href={ROUTES.SIGN_IN}>
            <Image src="/icons/account.svg" alt="Account" width={20} height={20} className="invert-colors lg:hidden" />
            <span className="primary-text-gradient max-lg:hidden">Sign In</span>
          </Link>
        </Button>
      )}
    </>
  );
};

export default LoginButton;
