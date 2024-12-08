import React from "react";

import ROUTES from "@/constants/routes";
import { Button } from "../button";
import Link from "next/link";
import Image from "next/image";
import { SheetClose } from "../sheet";
const LoginButtonMobile = ({ session }: { session: boolean }) => {
  return (
    <>
      {!session && (
        <SheetClose asChild>
          <Link href={ROUTES.SIGN_IN}>
            <Button className="small-medium btn-secondary min-h-[41px] w-full rounded-lg px-4 py-3">
              <Image src="/icons/account.svg" alt="Account" width={20} height={20} className="invert-colors lg:hidden" />
              <span className="text-dark300_light900 flex items-center justify-start gap-4 bg-transparent p-4">Sign In</span>
            </Button>
          </Link>
        </SheetClose>
      )}
    </>
  );
};

export default LoginButtonMobile;
